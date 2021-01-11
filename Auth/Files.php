<?php
namespace Auth;

use Auth\ClassNotes;

class Files{
    public $ProjectFactory;
    public $Project;
    public $ProjectFiles;
    protected $ClassNotes;

    protected $Files = [];
    protected $files;
    protected $Dirs = [];
    protected $Data = [];

    public function __construct(Array $Lists = [])
    {
        $this->ClassNotes = new ClassNotes();
        $this->ProjectFactory = \phpDocumentor\Reflection\Php\ProjectFactory::createInstance();
        foreach($Lists as $List){
            if(is_dir($List)){
                $this->Dirs[] = $List;
                $files = $this->scandir_php($List);
                foreach($files as $file){
                    $this->Files[] = $file;
                    $this->ProjectFiles[] = new \phpDocumentor\Reflection\File\LocalFile($file);
                }
            }else if(is_file($List)){
                $this->Files[] = $List; 
                $this->ProjectFiles[] = new \phpDocumentor\Reflection\File\LocalFile($List);
            }
        }
        if(count($this->Files) > 0){
            $this->Project = $this->ProjectFactory->create('My Project', $this->ProjectFiles);
        }
        $this->files = $this->Project->getFiles();
    }

    public function getDirs(){
        return $this->Dirs;
    }

    public function getFiles(){
        return $this->Files;
    }

    public function setDirs($dirs){
        $this->Dirs = $dirs;
        return $this;
    }

    public function setFiles($files){
        $this->Files = $files;
        return $this;
    }

    public function getAllData(){
        $this->Data = [];
        $ClassNotes = new ClassNotes();
        foreach($this->files as $file){
            $classesData = [];
            foreach($file->getClasses() as $classPath=>$class){
                $classesData[$classPath] = $ClassNotes->getClassData($class, $classPath);
            }
            $this->Data[$file->getPath()] = $classesData;
        }
        return $this->Data;
    }

    public function readDir($dirs){
        $data = [];
        if(is_dir($dirs)){
            $data = $this->scandir_php($dirs);
        }else if(is_array($dirs)){
            foreach($dirs as $dir){
                $data[] = $this->scandir_php($dir);
            }
        }
        return $data;
    }

    public function getFileData($file){
        $fileName = $file->getName();
        $fileHash = $file->getHash();
        $filePath = $file->getPath();
        $fileSource = $file->getSource();

    }

    public function readFiles($files){
        $data = [];
        if(is_file($files)){
            if(substr($files, strlen($files)-4) == '.php'){
                $data = $files;
            }
        }else if(is_array($data)){
            foreach($files as $file){
                if(substr($files, strlen($files)-4) == '.php'){
                    $data[] = $files;
                }
            }
        }
        return $data;
    }

    protected function scandir_php($dir)
    {
        //定义一个数组
        $files = array();
        //检测是否存在文件
        if (is_dir($dir)) {
            //打开目录
            if ($handle = opendir($dir)) {
                //返回当前文件的条目
                while (($file = readdir($handle)) !== false) {
                    //去除特殊目录
                    if ($file != "." && $file != "..") {
                        //判断子目录是否还存在子目录
                        if (is_dir($dir . "\\" . $file)) {
                            //递归调用本函数，再次获取目录
                            $files[$file] = $this->scandir_php($dir . "\\" . $file);
                        } else {
                            if(substr($file, strlen($file)-4) == '.php'){
                                //获取目录数组
                                $files[] = $dir . "\\" . $file;
                            }
                        }
                    }
                }
                //关闭文件夹
                closedir($handle);
                //返回文件夹数组
                return $files;
            }
        }
    }

}