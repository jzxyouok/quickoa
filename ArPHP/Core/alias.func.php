<?php
/**
 * ArPHP A Strong Performence PHP FrameWork ! You Should Have.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Core.base
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.org/licence MIT Licence
 * @version  GIT: 1: coding-standard-tutorial.xml,v 1.0 2014-5-01 18:16:25 cweiske Exp $
 * @link     http://www.arphp.org
 */

/**
 * component holder.
 *
 * @param string $name cname.
 *
 * @return Object
 */
function arComp($name = '')
{
    return Ar::c($name);

}

/**
 * alas to cfg.
 *
 * @param string $name key of config.
 *
 * @return mixed
 */
function arCfg($name = '', $default = 'NOT_RGI')
{
    if ($default === 'NOT_RGI') :
        return Ar::getConfig($name);
    else :
        return Ar::getConfig($name, $default);
    endif;

}

/**
 * route holder.
 *
 * @param string $name    route path.
 * @param mixed  $params  route param.
 * @param string $urlMode url mode.
 *
 * @return string
 */
function arU($name = '', $params = array(), $urlMode = 'NOT_INIT')
{
    return arComp('url.route')->createUrl($name, $params, $urlMode);

}

/**
 * arModule.
 *
 * @param string $name moduleName.
 *
 * @return Module
 */
function arModule($name = '')
{
    static $moduleList = array();
    $module = $name . 'Module';
    if (!array_key_exists($module, $moduleList)) :
        arComp('ext.out')->deBug('|MODULE_INIT:' . $module .'|');
        $moduleList[$module] = new $module;
        if (is_callable(array($moduleList[$module], 'initModule'))) :
            call_user_func_array(array($moduleList[$module], 'initModule'), array());
        endif;
    endif;
    if (AR_DEBUG && !AR_AS_CMD) :
        arComp('ext.out')->deBug('|MODULE_EXEC:' . $module .'|');
    endif;
    return $moduleList[$module];

}


/**
 * filter $_GET.
 *
 * @param string $key     get key.
 * @param mixed  $default return value.
 *
 * @return mixed
 */
function arGet($key = '', $default = null)
{
    $getUrlParamsArray = arComp('url.route')->parseGetUrlIntoArray();
    $ret = array();

    if (empty($key)) :
        $ret = $getUrlParamsArray;
    else :
        if (!isset($getUrlParamsArray[$key])) :
            $ret = null;
        else :
            $ret = $getUrlParamsArray[$key];
        endif;
    endif;

    $ret = arComp('format.format')->addslashes($ret);
    if (is_numeric($ret) && $ret < 2147483647 && strlen($ret) == 1) :
        $ret = (int)$ret;
    elseif (empty($ret)) :
        $ret = $default;
    endif;

    return arComp('format.format')->trim($ret);

}

/**
 * filter $_POST.
 *
 * @param string $key     post key.
 * @param mixed  $default return value.
 *
 * @return mixed
 */
function arPost($key = '', $default = null)
{
    $ret = array();

    if (empty($key)) :
        $ret = $_POST;
    else :
        if (!isset($_POST[$key])) :
            $ret = $default;
        else :
            $ret = $_POST[$key];
        endif;
    endif;

    return arComp('format.format')->addslashes(arComp('format.format')->trim($ret));

}

/**
 * filter $_REQUEST 有缓冲.
 *
 * @param string $key      post      key.
 * @param mixed  $default  return    value.
 * @param array  $addArray add merge array.
 *
 * @return mixed
 */
function arRequest($key = '', $default = null, $addArray = array())
{
    static $request = array();
    if (empty($request) || !empty($addArray)) :
        if (!is_array($addArray)) :
            $addArray = array();
        endif;
        $getArr = arGet('', array());
        $postArr = arPost('', array());
        $request = array_merge($getArr, $postArr, $addArray);
        $request = arComp('format.format')->addslashes(arComp('format.format')->trim($request));
    endif;

    if ($key) :
        if (array_key_exists($key, $request)) :
            $ret = $request[$key];
        else :
            $ret = $default;
        endif;
    else :
        $ret = $request;
    endif;

    return $ret;

}

/**
 * load other module.
 *
 * @param string $module name.
 *
 * @return mixed
 */
function arLm($module)
{
    return Ar::importPath(AR_ROOT_PATH . str_replace('.', DS, $module));

}

/**
 * echo for default
 *
 * @param string $echo    echo.
 * @param string $default default out.
 * @param string $key     key.
 *
 * @return void
 */
function arEcho($echo = '', $default = '', $key = '')
{
    if (is_array($default)) :
        $index = (int)$echo;
        if (arComp('validator.validator')->checkMutiArray($default)) :
            $echo = !empty($default[$index]) && !empty($default[$index][$key]) ? $default[$index][$key] : '';
        else :
            $echo = empty($default[$index]) ? '' : $default[$index];
        endif;
    else :
        if (empty($echo)) :
            $echo = $default;
        endif;
    endif;

    echo $echo;

}

/**
 * Html segment.
 *
 * @param string $seg html 片段 通过 $this->assign 分配.
 *
 * @return void
 */
function arSeg($segment)
{
    if (!is_array($segment)) :
        throw new ArException("segment must be an array");
    endif;

    if (empty($segment['segKey'])) :
        $keyBundle = array_keys($segment);
        $segKey = $keyBundle[0];
    else :
        $segKey = $segment['segKey'];
    endif;
    extract($segment);
    $segFile = arCfg('DIR.SEG') . str_replace('/', DS, $segKey) . '.seg';
    if (!is_file($segFile)) :
        $segFile .= '.php';
        if (!is_file($segFile)) :
            throw new ArException("segment file " . $segFile . ' not found');
        endif;
    endif;
    include $segFile;

}
