<?php

namespace Auth;

/**
 * Tags 处理类
 * Tags 列表 https://docs.phpdoc.org/latest/references/phpdoc/tags/var.html
 */
class Tags{
    
    /**
     * 方法的注释标签列表
     *
     * @var array
     */
    protected $MethodTags = [
        'author'=>'NotesAuthor',
        'param'=>'NotesParam',
        'version'=>'NotesVersion',
        'link'=>'NotesLink',
        'auth'=>'NotesAuth',
        'api'=>'NotesApi',
        'package'=>'NotesPackage',
        'category'=>'NotesCategory',
        'copyright'=>'NotesCopyright',
        'deprecated'=>'NotesDeprecated',
        'example'=>'NotesExample',
        'filesource'=>'NotesFilesource',
        'global'=>'NotesGlobal',
        'ignore'=>'NotesIgnore',
        'internal'=>'NotesInternal',
        'license'=>'NotesLicense',
        'method'=>'NotesMethod',
        'property'=>'NotesProperty',
        'property-read'=>'NotesPropertyRead',
        'property-write'=>'NotesPropertyWrite',
        'return'=>'NotesReturn',
        'see'=>'NotesSee',
        'since'=>'NotesSince',
        'source'=>'NotesSource',
        'subpackage'=>'NotesSubpackage',
        'throws'=>'NotesThrows',
        'todo'=>'NotesTodo',
        'uses'=>'NotesUses',
        'var'=>'NotesVar',
    ];

    public $ExtendMethodTags = [];

    /**
     * 类的注释标签列表a
     *
     * @var array
     */
    protected $ClassTags = [
        'author'=>'NotesAuthor',
        'param'=>'NotesParam',
        'version'=>'NotesVersion',
        'link'=>'NotesLink',
        'auth'=>'NotesAuth',
        'api'=>'NotesApi',
        'package'=>'NotesPackage',
        'category'=>'NotesCategory',
        'copyright'=>'NotesCopyright',
        'deprecated'=>'NotesDeprecated',
        'example'=>'NotesExample',
        'filesource'=>'NotesFilesource',
        'global'=>'NotesGlobal',
        'ignore'=>'NotesIgnore',
        'internal'=>'NotesInternal',
        'license'=>'NotesLicense',
        'method'=>'NotesMethod',
        'property'=>'NotesProperty',
        'property-read'=>'NotesPropertyRead',
        'property-write'=>'NotesPropertyWrite',
        'return'=>'NotesReturn',
        'see'=>'NotesSee',
        'since'=>'NotesSince',
        'source'=>'NotesSource',
        'subpackage'=>'NotesSubpackage',
        'throws'=>'NotesThrows',
        'todo'=>'NotesTodo',
        'uses'=>'NotesUses',
        'var'=>'NotesVar',
    ];
    
    public $tag;
    protected $tagsList = [];

    public function __construct()
    {

    }

    /**
     * 处理标签注释方法
     *
     * @param [type] $tags
     * @param string $type function or class
     * @return Array
     */
    public function NotesTags($tags, $type = 'function'){
        $this->tagsList = [];
        $methodName = '';
        if($type == 'function'){
            $typeTags = $this->MethodTags;
        }else{
            $typeTags = $this->ClassTags;
        }
        foreach($tags as $tag){
            $this->tag = $tag;
            $tagName = $tag->getName();
            //扩展标签处理
            if(array_key_exists($tagName, $this->ExtendMethodTags)){
                $methodName = $this->ExtendMethodTags[$tagName];
                if(is_object($methodName)){
                    $data = '';
                    if(method_exists($methodName, 'handleTag')){
                        $rm = new \ReflectionMethod($methodName, 'handleTag');
                        if ($rm->isStatic()) {
                            $data = $methodName::handleTag($tag, $tagName);
                        } else {
                            $data = $methodName->handleTag($tag, $tagName);
                        }
                    }
                    else{
                        $data = $methodName($tag, $tagName);
                    }
                    $this->tagReturn($data, $tagName);
                }
            }
            else if(array_key_exists($tagName, $typeTags)){//默认标签处理
                $methodName = $typeTags[$tagName];
                if(method_exists($this, $methodName)){
                    $this->$methodName($tagName);
                }
            }else{
                $this->NotesOther($tagName);
            }
        }
        return $this->tagsList;
    }

    /**
     * Author 标签的处理
     *
     * @param [type] $tag
     * @return void
     */
    public  function NotesAuthor($name){
        $data = [];
        $data['authorName'] = $this->tag->getAuthorName();
        $data['authorEmail'] = $this->tag->getEmail();
        $data['description'] = $this->tag->getDescription();
        $this->tagsList[$name] = $data;
        return $this->tagsList[$name];
    }

    /**
     * Param 标签的处理
     *
     * @param [type] $tag
     * @param [type] $params
     * @return void
     */
    public function NotesParam($name){
        $Description = null;
        if(method_exists($this->tag, 'getDescription')){
            $Description = $this->tag->getDescription();
            if(empty($Description)){
                $text = '';
            }else{
                $text = $this->tag->getDescription()->getBodyTemplate();
            }
            
            $isVariadic = $this->tag->isVariadic();
            $types = $this->tag->getType()->__toString();
            $this->tagsList[$name][$this->tag->getVariableName()] = [
                'text'=>$text,
                'isVariadic'=>$isVariadic,
                'types'=>explode('|',$types),
            ];
            return $this->tagsList[$name];
        }else{
            return [];
        }
    }

    /**
     * Version 标签的处理
     *
     * @param [type] $tag
     * @return void
     */
    public function NotesVersion($name){
        $this->tagsList[$name] = $this->tag->getVersion();
        return $this->tagsList[$name];
    }

    /**
     * Link 标签的处理
     *
     * @param [type] $tag
     * @return void
     */
    public function NotesLink($name){
        $this->tagsList[$name] = $this->tag->getLink();
        return $this->tagsList[$name];
    }

    /**
     * Auth 标签的处理
     *
     * @param [type] $tag
     * @return void
     */
    public function NotesAuth($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $auth = 0;

        if($text === 0 || $text === '0' || $text === 'false'){
            $auth = 0;
        }else{
            $auth = 1;
        }
        $this->tagsList[$name] = $auth;
        return $this->tagsList[$name];
    }
    
    public function NotesApi($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $this->tagsList[$name] = $text;
        return $this->tagsList[$name];
    }

    public function NotesCategory($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $this->tagsList[$name] = $text;
        return $this->tagsList[$name];
    }

    public function NotesCopyright($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $this->tagsList[$name] = $text;
        return $this->tagsList[$name];
    }

    public function NotesDeprecated($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $version = $this->tag->getVersion();
        $this->tagsList[$name][] = ['version'=>$version,'text'=>$text];
        return $this->tagsList[$name];
    }

    public function NotesExample($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $this->tagsList[$name][] = $text;
        return $this->tagsList[$name];
    }

    public function NotesFilesource($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $this->tagsList[$name] = $text;
        return $this->tagsList[$name];
    }
    
    public function NotesGlobal($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $this->tagsList[$name] = $text;
        return $this->tagsList[$name];
    }

    public function NotesIgnore($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $this->tagsList[$name] = $text;
        return $this->tagsList[$name];
    }

    public function NotesInternal($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $this->tagsList[$name] = $text;
        return $this->tagsList[$name];
    }

    public function NotesLicense($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $this->tagsList[$name][] = $text;
        return $this->tagsList[$name];
    }

    public function NotesMethod($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $methodName = $this->tag->getMethodName();
        $arguments = $this->tag->getArguments();
        $this->tagsList[$name][] = [
            'methodName'=>$methodName,
            'arguments'=>$arguments,
            'text'=>$text,
        ];
        return $this->tagsList[$name];
    }

    public function NotesPackage($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $this->tagsList[$name] = $text;
        return $this->tagsList[$name];
    }

    public function NotesProperty($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $this->tagsList[$name][$this->tag->getVariableName()] = $text;
        return $this->tagsList[$name];
    }

    public function NotesPropertyRead($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $this->tagsList[$name][$this->tag->getVariableName()] = $text;
        return $this->tagsList[$name];
    }

    public function NotesPropertyWrite($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $this->tagsList[$name][$this->tag->getVariableName()] = $text;
        return $this->tagsList[$name];
    }

    public function NotesReturn($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        
        $types = $this->tag->getType()->__toString();

        $this->tagsList[$name] = [
            'text'=>$text,
            'types'=>explode('|',$types),
        ];
        return $this->tagsList[$name];
    }

    public function NotesSee($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $uri = $this->tag->getReference()->__toString();
        $this->tagsList[$name][] = [
            'text'=>$text,
            'uri'=>$uri,
        ];
        return $this->tagsList[$name];
    }

    public function NotesSince($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $version = $this->tag->getVersion();
        $this->tagsList[$name][] = ['version'=>$version,'text'=>$text];
        return $this->tagsList[$name];
    }

    public function NotesSource($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $this->tagsList[$name] = [
            'text'=>$text,
            'startingLine'=>$this->tag->getStartingLine(),
            'lineCount'=>$this->tag->getLineCount(),
        ];
        return $this->tagsList[$name];
    }

    public function NotesSubpackage($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $this->tagsList[$name] = $text;
        return $this->tagsList[$name];
    }

    public function NotesThrows($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $types = $this->tag->getType();

        $this->tagsList[$name] = [
            'text'=>$text,
            'namespace'=>$types->__toString(),
            'name'=>$types->getFqsen()->getName(),
        ];
        return $this->tagsList[$name];
    }

    public function NotesTodo($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $this->tagsList[$name] = $text;
        return $this->tagsList[$name];
    }

    public function NotesUses($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $refers = $this->tag->getReference();

        $this->tagsList[$name] = [
            'text'=>$text,
            'namespace'=>$refers->__toString(),
            'name'=>$refers->getName(),
        ];
        return $this->tagsList[$name];
    }

    public function NotesUsedBy($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $refers = $this->tag->getReference();

        $this->tagsList[$name] = [
            'text'=>$text,
            'namespace'=>$refers->__toString(),
            'name'=>$refers->getFqsen()->getName(),
        ];
        return $this->tagsList[$name];
    }

    public function NotesVar($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $variableName = $this->tag->getVariableName();
        $types = $this->tag->getType();
        
        $this->tagsList[$name][] = [
            'text'=>$text,
            'name'=>$variableName,
            'types'=>explode('|', $types->__toString()),
        ];
        return $this->tagsList[$name];
    }

    /**
     * 方法其他注释处理
     *
     * @param [type] $tag
     * @return void
     */
    public function NotesOther($name){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        $this->tagsList[$name] = $text;
        return $this->tagsList[$name];
    }

    public function tagReturn($data,string $name){
        if(is_array($data)){
            if(count($data) > 1){
                foreach($data as $val){
                    $this->tagsList[$name][] = $val;
                }
            }else{
                $this->tagsList[$name] = $data;
            }
        }else{
            $this->tagsList[$name] = $data;
        }
    }

    /**
     * 自定义标签处理
     *
     * @param [type] $function($tag, $text)
     * @return void
     */
    public function CustomNotesOther($function){
        $Description = $this->tag->getDescription();
        if(empty($Description)){
            $text = '';
        }else{
            $text = $this->tag->getDescription()->getBodyTemplate();
        }
        return $function($this->tag, $text);
    }
}