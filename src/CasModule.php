<?php

/**
 * @license MIT License
 */

namespace daxslab\yii2casclient\cas;
use yii\base\Exception;

/**
 * A Yii2 Module that will handle the HTTP query of the CAS server.
 *
 * @author François Gannaz <francois.gannaz@poofe.info>
 */
class CasModule extends \yii\base\Module
{
    /**
     * @var array Must be filled by the declaration of the module
     */
    public $config;

    /**
     * @var string
     */
    public $controllerNamespace = 'daxslab\yii2casclient\cas\controllers';


    public $profileClass;
    public $userClass;

    /**
     * @var CasService
     */
    protected $casService;

    public function init()
    {
        parent::init();

        if (!$this->profileClass || !class_exists($this->profileClass)){
            throw new Exception('The profile class doesn\'t exists or is empty');
        }

        if (!$this->userClass || !class_exists($this->userClass)){
            throw new Exception('The user class doesn\'t exists or is empty');
        }

        $this->config['profileClass'] = $this->profileClass;
        $this->config['userClass'] = $this->userClass;
        $this->casService = new CasService($this->config);
    }

    public function getCasService()
    {
        return $this->casService;
    }
}
