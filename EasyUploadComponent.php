<?php

/**
 * Upload files and Images easy with EasyUpload Component
 *
 *
 * PHP 5
 *
 * Copyright 2013, Andrew Esteves
 *
 *
 * @copyright     Copyright 2013 Andrew Esteves (http://andrewesteves.com.br)
 * @link          https://github.com/andrewesteves/easycomponent
 * @author        Andrew Esteves
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Component', 'Controller');

class EasyUploadComponent extends Component{
    
    public $components = array('Session');

    private $_file_name;
    private $_tmp_name;
    private $_file_size;
    private $_ext;
    private $_error;
    private $_src;
    private $_resize;
    private $_img_crop;
    private $_path;
    
	public function uploader($model, $request, $lastId, $foreignKey){

		$path = WWW_ROOT . '/img/noticias/' . $lastId . '/';
		
        $this->_path = $path;
        
        
		if(!is_dir($path))
			@mkdir($path, 0777);

		foreach($request as $key => $value){
            
            $this->setFileName($value['name']);
            $this->setExt($value['name']);
            $this->resizeImg($value['tmp_name']);
            $this->cropImg($value['tmp_name']);
            
			$arr = array(
				$foreignKey => $lastId,
				'photo' => strtolower($value['name']),
                'dir'   => $lastId,
                'mimetype' => $value['type'],
                'filesize' => $value['size']
			);

			move_uploaded_file($value['tmp_name'], $path . strtolower($value['name']));

			$model->saveAll($arr);
		}

	}
    
    private function setFileName($file){
        if(is_array($file)){
            
            $file = $file['name'];
            $pos  = strrpos($file, '.');
            $file = substr($file, 0, $pos);
            
            $this->_file_name = $file;
            
        }else{
            
            $pos  = strrpos($file, '.');
            $file = substr($file, 0, $pos);
            
            $this->_file_name = $file;
        }
    }
    
    private function getFileName(){ return strtolower($this->_file_name); }
    
    private function setExt($file){
        $ext = explode(".",$file);
		$ext = strtolower(end($ext));
        $this->_ext = $ext;
    }
    
    private function getExt(){ return $this->_ext; }
    
        
    private function fileExists(){
    
       if($handle = opendir($path)){
			while(false !== ($file = readdir($handle))){
				if($file != '.' && $file != '..'){
					if(file_exists($file)){
                       $this->_error = true;
                    }
				}
			}
		}
        
    }
    
    private function resizeImg($file){
       
        list($width, $height) = getimagesize($file);
        
        $new_width = $width/2;
        $new_height = ( $height * $new_width ) / $width;
        
        switch($this->getExt()){
            case 'jpg':
                $this->_src = imagecreatefromjpeg($file);
                break;
            case 'png':
                $this->_src = imagecreatefrompng($file);
                break;
            case 'gif':
                $this->_src = imagecreatefromgif($file);
                break;
            case 'pjpeg':
                $this->_src = imagecreatefromjpeg($file);
                break;
            default:
                $this->_src = imagecreatefromjpeg($file);
        }
        
        $this->_resize = imagecreatetruecolor($new_width, $new_height);
        $color = imagecolorallocate($this->_resize, 255, 255, 255);
        imagefill($this->_resize, 0, 0, $color);
        
        imagecopyresampled($this->_resize, $this->_src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        imagejpeg($this->_resize, $this->_path . $this->getFileName(). '_thumb.jpg' );
        imagedestroy($this->_resize);        
    }
    
    private function cropImg($file){
        
        list($width, $height) = getimagesize($file);
        
        $new_width = 250;
        $new_height = 200;
        
        $dst_w = $new_width;
        $dst_h = ( $height * $new_width ) / $width;
        
        if($dst_w < $new_width || $dst_h < $new_height){
            $dst_w += 100;
            $dst_h += 100;
        }
        
        switch($this->getExt()){
            case 'jpg':
                $this->_src = imagecreatefromjpeg($file);
                break;
            case 'png':
                $this->_src = imagecreatefrompng($file);
                break;
            case 'gif':
                $this->_src = imagecreatefromgif($file);
                break;
            case 'pjpeg':
                $this->_src = imagecreatefromjpeg($file);
                break;
            default:
                $this->_src = imagecreatefromjpeg($file);
        }
        
        $this->_img_crop = imagecreatetruecolor($new_width, $new_height);
        $color = imagecolorallocate($this->_img_crop, 255, 255, 255);
        imagefill($this->_img_crop, 0, 0, $color);
        
        imagecopyresampled($this->_img_crop, $this->_src, 0, 0, 0, 0, $dst_w, $dst_h, $width, $height);
        imagejpeg($this->_img_crop, $this->_path . $this->getFileName() . '_crop.jpg' );
        imagedestroy($this->_img_crop);
    }
    
}