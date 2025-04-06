<?php

namespace App\Service;

use App\Entity\Fichier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Upload file service
 */
class UploadFileService{

    private $param;
    private $_web_root;

    /**
     * Constructor UploadFileService
     *
     * @param ParameterBagInterface $param
     */
    public function __construct(EntityManagerInterface $_entity_manager,ParameterBagInterface $param)
    {
        $this->_web_root       = realpath($param->get('kernel.project_dir') . '/public');
        $this->_entity_manager = $_entity_manager;
        $this->param = $param;
    }

    /**
     * upload file
     *
     * @param Object $form
     * @param Object $entity_name
     * @param String $entity_property
     * @param String $postfix
     * @return void
     */
    public function uploadFile($form, $entity_name, $entity_property, $service_parameter)
    {
        $file_uploaded = $form->get($entity_property)->getData();
        $method_set = 'set'.ucfirst($entity_property); 

        if (!file_exists($this->param->get($service_parameter))) {
            mkdir($this->param->get($service_parameter), 0777, true);
        }

        if(is_object($file_uploaded)){
            $destination = $this->param->get($service_parameter);
            $file = md5(uniqid()).'.'.$file_uploaded->guessExtension();

            $file_uploaded->move(
                $destination,
                $file
            );
            
            $entity_name->$method_set($file);
        }
    }

    /**
     * update file uploaded
     *
     * @param Object $form
     * @param Object $entity_name
     * @param String $entity_property
     * @param String $postfix
     * @return void
     */
    public function updateFileUploaded($form, $entity_name, $entity_property, $service_parameter)
    {
        $file_uploaded = $form->get($entity_property)->getData();
        $method_set = 'set'.ucfirst($entity_property); 

        if(is_object($file_uploaded)){
            $destination = $this->param->get($service_parameter);
            $file = md5(uniqid()).'.'.$file_uploaded->guessExtension();
            
            $this->deleteFile($entity_name, $entity_property, $service_parameter);

            $file_uploaded->move(
                $destination,
                $file
            );
            
            $entity_name->$method_set($file);
        }
    }

    /**
     * Delete file
     *
     * @param Object $entity_name
     * @param String $postfix
     * @param String $service_parameter
     * @return void
     */
    public function deleteFile($entity_name, $entity_property, $service_parameter)
    {
        $method_get = 'get'.ucfirst($entity_property);
        $file = $entity_name->$method_get();

        if ($file != "") {
           $old_file = $this->param->get($service_parameter).'/'.$file;
           unlink($old_file);
        }
    }


    /**
     * Save File
     * @param $_file
     * @param $_nature_file
     * @param $_directory
     * @param null $_last_file
     * @return FctFile|bool|null
     * @throws \Exception
     */
    public function saveFile($_file, $_nature_file, $_directory, $_last_file = null)
    {
        $_files = new Fichier();
        if (is_object($_last_file)) {
            $_files = $_last_file;
            $_path      = $this->_web_root . $_last_file->getUrlFichier();
            if (file_exists($_path)) @unlink($_path);
        }
        // Retrieve the specific directory
        try {
            $_original_name = $_file->getClientOriginalName();
            $_path_part     = pathinfo($_original_name);
            $_extension     = $_path_part['extension'];
            $_filename      = md5(uniqid());

            // Upload file
            $_filename_extension = $_filename . '.' . $_extension;
            $_uri_file           = $_directory . $_filename_extension;
            $_dir                = $this->_web_root . $_directory;
            $_file->move($_dir, $_filename_extension);
            $_files->setExtFichier($_extension);
            $_files->setNomFichier($_original_name);
            $_files->setUrlFichier($_uri_file);
            $_files->setNatFichier($_nature_file);

            $this->_entity_manager->persist($_files);
            $this->_entity_manager->flush();

            $_response = $_files;
        } catch (\Exception $_exc) {
            dd($_exc);
            $this->_utils_manager->setFlash('error', $_exc->getMessage());
            $_response = false;
        }

        return $_response;
    }
}