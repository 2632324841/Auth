<?php

namespace Auth;

use Auth\Tags;

class ClassNotes{

    protected $Classes;
    protected $Methods;
    protected $Tags;

    public function __construct()
    {
        $this->Tags = new Tags();
    }

    public function readClasses($file){
        $this->Classes = $file->getClasses();
        return $this;
    }

    public function readMethods($classes = null){
        if(empty($classes)){
            $classes = $this->Classes;
        }
        if(is_array($classes)){
            foreach($classes as $class){
                $this->Methods[$class->getFqsen()->__toString()] = $class->getMethods();
            }
        }else{
            $this->Methods[$classes->getFqsen()->__toString()] = $classes->getMethods();
        }
    }

    /**
     * 获取类注释的内容
     *
     * @param [type] $class
     * @param [type] $classPath
     * @return void
     */
    public function getClassData($class, $classPath){
        $className = $class->getName();
        $classDocBlock = $class->getDocBlock();
        $classNamespace = $class->getFqsen()->__toString();
        $location = [];
        $classTags = [];
        $classDocBlockData =[];
        $summary = '';
        if(!empty($class->getLocation())){
            $location = [
                'lineNumber'=>$class->getLocation()->getLineNumber(),
                'columnNumber'=>$class->getLocation()->getColumnNumber(),
            ];
        }
        if(!empty($classDocBlock)){
            
            $classTag = $classDocBlock->getTags();
            $summary = $classDocBlock->getSummary();
            $namespace = $classDocBlock->getContext()->getNamespace();
            $namespaceAliases = $classDocBlock->getContext()->getNamespaceAliases();
            
            $classTags = $this->Tags->NotesTags($classTag, 'class');
            $docBlockLocation = [];
            if(!empty($classDocBlock->getLocation())){
                $docBlockLocation = [
                    'lineNumber'=>$classDocBlock->getLocation()->getLineNumber(),
                    'columnNumber'=>$classDocBlock->getLocation()->getColumnNumber(),
                ];
            }
            $classDocBlockData = [
                'summary'=>$summary,
                'namespace'=>$namespace,
                'namespaceAliases'=>$namespaceAliases,
                'tags'=>$classTags,
                'isTemplateStart'=>$classDocBlock->isTemplateStart(),
                'isTemplateEnd'=>$classDocBlock->isTemplateEnd(),
                'location'=>$docBlockLocation,
            ];
        }

        
        $usedTraits = $class->getUsedTraits();
        $Constants = $class->getConstants();
        $properties = $class->getProperties();
        if(empty($class->getParent())){
            $parent['fqsen'] = '';
            $parent['name'] = '';
        }
        else
        {
            $parent['fqsen'] = $class->getParent()->__toString();
            $parent['name'] = $class->getParent()->getName();
        }
        $isAbstract = $class->isAbstract();
        $isFinal = $class->isFinal();
        $implements = $class->getInterfaces();
        
        $Mtethods = $class->getMethods();
        $methods = [];
        foreach($Mtethods as $Mtethod){
            $temp = $this->getMethodData($Mtethod);
            $methods[$temp['name']] = $temp;
        }

        $ClassData = [
            'name'=>$className,
            'path'=>$classPath,
            'namespace'=>$classNamespace,
            'docBlock'=>$classDocBlockData,
            'summary'=>$summary,
            'implements'=>$implements,
            'parent'=>$parent,
            'properties'=>$properties,
            'constants'=>$Constants,
            'usedTraits'=>$usedTraits,
            'isAbstract'=>$isAbstract,
            'isFinal'=>$isFinal,
            'tags'=>$classTags, 
            'location'=>$location,
            'methods'=>$methods,
        ];
        if(array_key_exists('auth', $classTags)){
            $ClassData['auth'] = $classTags['auth'];
        }else{
            $ClassData['auth'] = 0;
        }
        return $ClassData;
    }

    /**
     * 获取方法的内容
     *
     * @param [type] $method
     * @param [type] $path
     * @return void
     */
    public function getMethodData($method){

        $name = $method->getFqsen()->getName();
        $fqsen = $method->getFqsen()->__toString();
        $docBlockData = [];
        $methodTags = [];
        $location = [];
        $docBlock = $method->getDocBlock();
        $arguments = $method->getArguments();
        $argumentsData = [];
        $summary = '';

        if(!empty($arguments)){
            foreach($arguments as $argument){
                $argumentsData[] = [
                    'name'=>$argument->getName(),
                    'type'=>$argument->getType()->__toString(),
                    'default'=>$argument->getDefault(),
                    'byReference'=>$argument->isByReference(),
                    'isVariadic'=>$argument->isVariadic(),
                ];
            }
        }
        if(!empty($method->getLocation())){
            $location = [
                'lineNumber'=>$method->getLocation()->getLineNumber(),
                'columnNumber'=>$method->getLocation()->getColumnNumber(),
            ];
        }
        if(!empty($docBlock)){
            $summary = $docBlock->getSummary();
            $context = $docBlock->getContext();
            $contextData = [];
            if(!empty($context)){
                $contextData['namespace'] = $context->getNamespace();
                $contextData['namespaceAliases'] = $context->getNamespaceAliases();
            }

            $docBlockLocation = [];
            if(!empty($docBlock->getLocation())){
                $docBlockLocation = [
                    'lineNumber'=>$docBlock->getLocation()->getLineNumber(),
                    'columnNumber'=>$docBlock->getLocation()->getColumnNumber(),
                ];
            }

            $tags = $docBlock->getTags();
            $methodTags = $this->Tags->NotesTags($tags);
            $docBlockData = [
                'summary'=>$summary,
                'context'=>$contextData,
                'location'=>$docBlockLocation,
                'tags'=>$methodTags,
                'isTemplateStart'=>$docBlock->isTemplateStart(),
                'isTemplateEnd'=>$docBlock->isTemplateEnd(),
            ];
        }
        $visibility = $method->getVisibility()->__toString();
        $isAbstract = $method->isAbstract();
        $isFinal = $method->isFinal();
        $isStatic = $method->isStatic();

        if($name == 'auth'){
            $a = 1;
        }

        //方法参数
        $params = [];
        $MtethodData = [
            'name'=>$name,
            'fqsen'=>$fqsen,
            'description'=>'',
            'summary'=>$summary,
            'isStatic'=>$isStatic,
            'docBlock'=>$docBlockData,
            'param'=>$params,
            'location'=>$location,
            'visibility'=>$visibility,
            'isAbstract'=>$isAbstract,
            'isFinal'=>$isFinal,
            'tags'=>$methodTags,
        ];
        if(array_key_exists('auth', $methodTags)){
            $MtethodData['auth'] = $methodTags['auth'];
        }else{
            $MtethodData['auth'] = 0;
        }
        return $MtethodData;
    }
}