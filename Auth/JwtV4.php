<?php
namespace Auth;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;

/**
 * JWT
 * @github https://github.com/lcobucci/jwt   4.0文档   https://github.com/lcobucci/jwt
 */
class JwtV4{

    protected $secret;
    protected $Issuer = '';
    protected $Audience = '';
    protected $Id = '';
    protected $IssuedAt = '';
    protected $Expiration = '';
    protected $NotBefore = '';
    protected $RelatedTo = '';
    protected $WithHeader = '';
    protected $SelfData = [];

    public function __construct(string $secret,string $Issuer=NULL,string $Audience=NULL,string $Id=NULL,int $IssuedAt=NULL,int $Expiration=NULL,int $NotBefore=NULL,Array $SelfData=[]){
        if(empty($secret)){
            throw new \Exception('加密密钥为空'); 
        }
        $this->secret = $secret;
        if(!empty($Issuer)){
            $this->Issuer = $Issuer;
        }
        if(!empty($Audience)){
            $this->Audience = $Audience;
        }
        if(!empty($Id)){
            $this->Id = $Id;
        }
        if(is_int($IssuedAt) || $IssuedAt > 0){
            $this->IssuedAt = $IssuedAt;
        }
        if(is_int($Expiration) && $Expiration > 0 && $Expiration > $IssuedAt){
            $this->Expiration = $Expiration;
        }
        if(is_int($NotBefore) || $NotBefore >= 0){
            $this->NotBefore = $NotBefore;
        }
        if(count($SelfData) > 0){
            $this->SelfData = $SelfData;
        }
    }

    /**
     * 设置密钥
     *
     * @param [type] $secret
     * @return this
     */
    public function setSecret($secret){
        $this->secret = $secret;
        return $this;
    }

    /**
     * 设置发布者
     *
     * @param string $Issuer
     * @return this
     */
    public function setIssuer(string $Issuer){
        $this->Issuer = $Issuer;
        return $this;
    }

    /**
     * 设置接收者
     *
     * @param string $Audience
     * @return this
     */
    public function setAudience(string $Audience){
        $this->Audience = $Audience;
        return $this;
    }
    
    /**
     * 设置唯一表示Id
     *
     * @param string $Id
     * @return this
     */
    public function setId(string $Id){
        $this->Id = $Id;
        return $this;
    }

    /**
     * 设置开始时间
     *
     * @param integer $IssuedAt
     * @return this
     */
    public function setIssuedAt(int $IssuedAt){
        $this->IssuedAt = $IssuedAt;
        return $this;
    }

    /**
     * 设置过期时间
     *
     * @param integer $Expiration
     * @return this
     */
    public function setExpiration(int $Expiration){
        $this->Expiration = $Expiration;
        return $this;
    }

    /**
     * 设置禁用时间（从开始时间计算）
     *
     * @param integer $NotBefore
     * @return this
     */
    public function setNotBefore(int $NotBefore){
        $this->NotBefore = $NotBefore;
        return $this;
    }

    /**
     * 设置自定义数据
     *
     * @param Array $SelfData
     * @return this
     */
    public function setSelfData(Array $SelfData){
        $this->SelfData = $SelfData;
        return $this;
    }

    /**
     * 设置与...有关
     *
     * @param string $RelatedTo
     * @return this
     */
    public function setRelatedTo(string $RelatedTo){
        $this->RelatedTo = $RelatedTo;
        return $this;
    }

    /**
     * 获取密钥
     *
     * @return void
     */
    public function getSecret(){
        return $this->secret;
    }

    /**
     * 获取发布者
     *
     * @return void
     */
    public function getIssuer(){
        return $this->Issuer;
    }

    /**
     * 获取接受者
     *
     * @return void
     */
    public function getAudience(){
        return $this->Audience;
    }

    /**
     * 获取用户唯一表示
     *
     * @return void
     */
    public function getId(){
        return $this->Id;
    }

    /**
     * 设置开始时间
     *
     * @return void
     */
    public function getIssuedAt(){
        return $this->IssuedAt;
    }

    /**
     * 设置结束时间
     *
     * @return void
     */
    public function getExpiration(){
        return $this->Expiration;
    }

    /**
     * 获取Token禁用时间（从开始时间计算）
     *
     * @return void
     */
    public function getNotBefore(){
        return $this->NotBefore;
    }

    /**
     * 获取自定义数据
     *
     * @return void
     */
    public function getSelfData(){
        return $this->SelfData;
    }

    /**
     * 获取与...有关数据
     *
     * @return void
     */
    public function getRelatedTo(){
        return $this->RelatedTo;
    }

    /**
     * 创建Token
     * 
     * @param string $Issuer 发布者
     * @param string $Audience 接收者
     * @param string $Id 对当前token设置的标识
     * @param integer $IssuedAt token创建时间
     * @param integer $Expiration 过期时间
     * @param integer $NotBefore 当前时间在这个时间前，token不能使用
     * @param Array $SelfData 自定义数据
     * @return string
     */
    public function createTkoen(){
        //创建
        $builder = new Builder();
        //$signer  = new Sha256();

        
        if(is_file($this->secret)){
            $secret = new Key("file://{$this->secret}");
        }else{
            $secret = $this->secret;
        }
        if(empty($this->Expiration)){
            $this->Expiration = time() - 1;
        }
        $config = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText($secret));
        $IssuedAt_data = new \DateTimeImmutable(date('Y-m-d H:i:s', $this->IssuedAt));
        $Expiration_data = new \DateTimeImmutable(date('Y-m-d H:i:s', $this->Expiration));
        $NotBefore_data = new \DateTimeImmutable(date('Y-m-d H:i:s', $this->NotBefore));

        //设置header和payload，以下的字段都可以自定义
        $builder->issuedBy($this->Issuer); //发布者
        $builder->permittedFor($this->Audience); //接收者
        $builder->identifiedBy($this->Id, false); //对当前token设置的标识
        $builder->issuedAt($IssuedAt_data, false); //token创建时间
        $builder->expiresAt($Expiration_data, false); //过期时间
        $builder->canOnlyBeUsedAfter($NotBefore_data, false); //当前时间在这个时间前，token不能使用

        //与...什么有关
        if(!empty($this->RelatedTo)){
            $builder->relatedTo($this->RelatedTo);
        }
        
        if(count($this->SelfData) > 0){
            foreach($this->SelfData as $key=>$val){
                $builder->withClaim($key, $val);
            }
        }

        //设置签名
        //$builder->sign($signer, $secret);
        //获取加密后的token，转为字符串
        $token = (string)$builder->getToken($config->signer(), $config->signingKey());

        return $token;
    }

    /**
     * 验证Token
     *
     * @param string $Token token
     * @param integer $endTime 结束时间添加
     * @return void
     */
    public function checkToken(string $Token, int $endTime = 7200){
        $signer  = new Sha256();

        $secret = $this->secret;

        $config = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText($secret));
        
        try {
            //解析token
            $parse = (new Parser())->parse($Token);
            $ValidationData = new ValidationData();
            //$Validator = new \Lcobucci\JWT\Validation\Validator();
            //$Validator->validate($Token, $config);
            //验证token合法性
            if (!$parse->verify($config->signer(), $config->signingKey())) {
                return false;
            }
        
            //验证是否已经过期
            if ($parse->isExpired()) {
                return false;
            }
            
            //获取数据
            $data = $parse->claims()->all();
            if(count($data['aud']) == 1){
                $data['aud'] = $data['aud'][0];
            }
            $data['iat'] = $data['iat']->getTimestamp();
            $data['exp'] = $data['exp']->getTimestamp();
            $data['nbf'] = $data['nbf']->getTimestamp();
            //转换格式

            if($data['iss'] != $this->Issuer){
                return false;
            }
            if($data['aud'] != $this->Audience){
                return false;
            }
            if($data['jti'] != $this->Id){
                return false;
            }


            foreach($this->SelfData as $key=>$val){
                if(array_key_exists($key, $data)){
                    if($val != $data[$key]){
                        return false;
                    }
                }else{
                    return false;
                }
            }
            $ValidationData->setCurrentTime(time() + $endTime);
            return $data;
            
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 自定义验证方法
     *
     * @param string $Token
     * @param function $customFunction param data
     * @return array|bool
     */
    public function customCheck(string $Token, $customFunction){
        $signer  = new Sha256();

        $secret = $this->secret;
        
        $config = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText($secret));

        try {
            //解析token
            $parse = (new Parser())->parse($Token);
            //验证token合法性
            if (!$parse->verify($config->signer(), $config->signingKey())) {
                return false;
            }
        
            $this_time = new \DateTimeImmutable(date('Y-m-d H:i:s', time()));
            //验证是否已经过期
            if ($parse->isExpired($this_time)) {
                return false;
            }
            
            //获取数据
            $data = $parse->claims()->all();
            //转换格式
            /*
            iat:DateTimeImmutable
            exp:DateTimeImmutable
            nbf:DateTimeImmutable
            */
            if(count($data['aud']) == 1){
                $data['aud'] = $data['aud'][0];
            }
            $data['iat'] = $data['iat']->getTimestamp();
            $data['exp'] = $data['exp']->getTimestamp();
            $data['nbf'] = $data['nbf']->getTimestamp();
            

            return $customFunction($data);
            
        } catch (\Exception $e) {
            return false;
        }
    }
} 