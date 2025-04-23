<?php
/**
 * 访客统计
 * 
 * @package VisitorLoggerPro    
 * @author 璇
 * @version 1.5.0
 * @link https://blog.ybyq.wang/
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 插件实现类
 */
class VisitorLogger_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        $db = Typecho_Db::get();
        $prefix = $db->getPrefix();
        
        // 创建访问日志表
        $db->query("CREATE TABLE IF NOT EXISTS `{$prefix}visitor_log` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `ip` varchar(50) NOT NULL,
            `country` varchar(100) NOT NULL,
            `time` datetime NOT NULL,
            `path` varchar(255) NOT NULL,
            `ua` varchar(255) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `ip` (`ip`),
            KEY `time` (`time`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        
        // 创建用户登录日志表
        $db->query("CREATE TABLE IF NOT EXISTS `{$prefix}user_login_log` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `ip` varchar(50) NOT NULL,
            `time` datetime NOT NULL,
            `user_id` int(11) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `ip` (`ip`),
            KEY `time` (`time`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }
    
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate()
    {
        // 禁用插件时不删除数据表，保留数据
    }
    
    /**
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        // 插件配置项
        $form->addInput(new Typecho_Widget_Helper_Form_Element_Text(
            'excludedIPs',
            null,
            '',
            '排除的IP地址',
            '每行一个IP地址，这些IP的访问将不会被记录'
        ));
    }
    
    /**
     * 个人用户的配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
        // 个人配置项
    }
    
    /**
     * 插件实现方法
     * 
     * @access public
     * @return void
     */
    public static function render()
    {
        // 插件实现
    }
} 
