<?php

/**
 * 访客统计动作处理
 * 
 * @package VisitorLoggerPro
 * @author 璇
 * @version 1.5.0
 * @link https://blog.ybyq.wang/
 */

class VisitorLogger_Action extends Typecho_Widget implements Widget_Interface_Do
{
    /**
     * 初始化
     */
    public function __construct($request, $response, $params = NULL)
    {
        parent::__construct($request, $response, $params);
    }

    /**
     * 渲染访客统计页面
     */
    public function render()
    {
        $options = Helper::options();
        if (!$options->plugin('VisitorLogger')->enableStats) {
            throw new Typecho_Widget_Exception(_t('访客统计功能未启用'));
        }

        // 检查用户权限
        if (!$this->user->pass('administrator', true)) {
            throw new Typecho_Widget_Exception(_t('禁止访问'), 403);
        }

        // 渲染模板
        require_once dirname(__FILE__) . '/visitor-stats.php';
    }

    /**
     * 获取统计数据
     */
    public function stats()
    {
        // 检查用户权限
        if (!$this->user->pass('administrator', true)) {
            throw new Typecho_Widget_Exception(_t('禁止访问'), 403);
        }

        $db = Typecho_Db::get();
        $options = Helper::options();

        // 获取访客统计数据
        $stats = array(
            'total' => $db->fetchObject($db->select('COUNT(*) AS total')->from('table.visitor_log'))->total,
            'today' => $db->fetchObject($db->select('COUNT(*) AS total')
                ->from('table.visitor_log')
                ->where('time >= ?', strtotime('today')))->total,
            'yesterday' => $db->fetchObject($db->select('COUNT(*) AS total')
                ->from('table.visitor_log')
                ->where(
                    'time >= ? AND time < ?',
                    strtotime('yesterday'),
                    strtotime('today')
                ))->total,
            'countries' => $db->fetchAll($db->select('country, COUNT(*) AS count')
                ->from('table.visitor_log')
                ->group('country')
                ->order('count', Typecho_Db::SORT_DESC)
                ->limit(20)),
            'routes' => $db->fetchAll($db->select('route, COUNT(*) AS count')
                ->from('table.visitor_log')
                ->group('route')
                ->order('count', Typecho_Db::SORT_DESC)
                ->limit(15))
        );

        // 输出 JSON 数据
        $this->response->setContentType('application/json');
        echo json_encode($stats, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 绑定动作
     */
    public function action()
    {
        $this->on($this->request->is('stats'))->stats();
        $this->on($this->request->is('render'))->render();
    }
}
