<?php

namespace Deimos\Config;

use Deimos\Helper\Helper;
use Deimos\Slice\Iterator;
use Deimos\Slice\Slice;

const PHP  = 1;
const YML  = 2;
const JSON = 3;

class Config extends Iterator
{

    /**
     * @var int
     */
    protected $type;

    /**
     * @var bool
     */
    protected $withParameters;

    /**
     * @var string
     */
    protected $root;

    /**
     * @var Slice|array
     */
    protected $parameters;

    /**
     * @var array
     */
    protected $extensions = [
        PHP  => '.php',
        YML  => '.yml',
        JSON => '.json',
    ];

    /**
     * Config constructor.
     *
     * @param Helper $helper
     * @param string $root
     * @param array  $options
     *
     * @throws Exceptions\PermissionDenied
     */
    public function __construct(Helper $helper, $root, array $options = [])
    {
        $this->helper = $helper;
        $this->type   = $options['extension'] ?? PHP;

        $this->withParameters =
            array_key_exists('withParameters', $options)
                ? $options['withParameters']
                : true;

        $this->root = realpath($root) . '/';

        if (!$this->root)
        {
            throw new Exceptions\PermissionDenied($root);
        }

        if ($this->withParameters)
        {
            $filePath = $this->filePath('_deimos');

            if ($filePath)
            {
                $slice = $this->slice($filePath);

                $this->setParameters($slice);
            }
        }
    }

    /**
     * @param Slice|array $parameters
     *
     * @return $this
     */
    public function setParameters($parameters)
    {
        $this->parameters     = $parameters;
        $this->withParameters = !empty($this->parameters);

        return $this;
    }

    /**
     * @param string $file
     *
     * @return string
     */
    protected function path($file)
    {
        return $this->root .
            str_replace('.', '/', $file) .
            $this->extensions[$this->type];
    }

    /**
     * @param string $file
     *
     * @return bool|string
     */
    protected function filePath($file)
    {
        return realpath($this->path($file));
    }

    /**
     * @param string $file
     *
     * @return bool
     */
    public function exists($file)
    {
        return $this->filePath($file) !== false;
    }

    /**
     * @param string $file
     *
     * @return mixed
     * @throws Exceptions\TypeNotFound
     */
    protected function requireFile($file)
    {
        switch ($this->type)
        {
            case PHP:
                return require $file;

            case YML:
                return \Symfony\Component\Yaml\Yaml::parse(file_get_contents($file));

            case JSON:
                return $this->helper->json()->decode(file_get_contents($file));
        }

        throw new Exceptions\TypeNotFound();
    }

    /**
     * @param string      $path
     * @param array|Slice $data
     *
     * @return bool|int
     * @throws Exceptions\TypeNotFound
     */
    public function saveFile($path, $data)
    {
        $storage = $data;

        if ($data instanceof Slice)
        {
            $storage = $data->asArray();
        }

        switch ($this->type)
        {
            case PHP:
                $result = '<?php'
                    . PHP_EOL
                    . PHP_EOL .
                    'return ' . var_export($storage, true) . ';';
                break;

            case YML:
                $result = \Symfony\Component\Yaml\Yaml::dump($storage);
                break;

            case JSON:
                $json = clone $this->helper->json();
                $json->addOption(JSON_PRETTY_PRINT);
                $result = $json->encode($storage);
                break;

            default:
                throw new Exceptions\TypeNotFound($this->type);
        }

        $filePath = $this->path($path);

        return file_put_contents($filePath, $result);
    }

    /**
     * @param array $storage
     *
     * @return Slice
     */
    public function make(array $storage)
    {
        return new Slice(
            $this->helper,
            $storage,
            $this->withParameters ? $this->parameters : null
        );
    }
    
    /**
     * @param string $file
     *
     * @return Slice
     */
    protected function slice($file)
    {
        if (empty($this->storage[$file]))
        {
            $require = $this->requireFile($file);

            $this->storage[$file] = $this->make($require);
        }

        return $this->storage[$file];
    }

    /**
     * @param string $path
     *
     * @return Slice|mixed
     * @throws Exceptions\PermissionDenied
     * @throws \Deimos\Helper\Exceptions\ExceptionEmpty
     */
    public function get($path)
    {
        $data = explode(':', $path, 2);
        $file = $this->filePath($data[0]);

        if (!$file)
        {
            throw new Exceptions\PermissionDenied($data[0]);
        }

        $slice = $this->slice($file);

        if (!empty($data[1]))
        {
            return $slice->getData($data[1]);
        }

        return $slice;
    }

}
