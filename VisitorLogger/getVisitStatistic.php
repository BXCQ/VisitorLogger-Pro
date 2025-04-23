<?php
/**
 * 获取访问统计数据
 * 
 * @package VisitorLogger
 * @author BXCQ
 * @version 1.0.0
 * @link https://blog.ybyq.wang/
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 统计数据获取类
 */
class VisitorLogger_Statistic
{
    /**
     * 获取访问统计数据
     * 
     * @access public
     * @param string $startDate 开始日期
     * @param string $endDate 结束日期
     * @return array
     */
    public static function getVisitStatistic($startDate = null, $endDate = null)
    {
        $db = Typecho_Db::get();
        $prefix = $db->getPrefix();
        
        // 构建查询条件
        $where = array();
        if ($startDate) {
            $where[] = "time >= '{$startDate}'";
        }
        if ($endDate) {
            $where[] = "time <= '{$endDate}'";
        }
        
        // 获取国家访问统计
        $countryStats = $db->fetchAll($db->select('country, COUNT(*) as count')
            ->from($prefix . 'visitor_log')
            ->where(implode(' AND ', $where))
            ->group('country')
            ->order('count', Typecho_Db::SORT_DESC));
            
        // 获取IP分布统计
        $ipStats = array();
        foreach ($countryStats as $stat) {
            $country = $stat['country'];
            $ips = $db->fetchAll($db->select('ip, COUNT(*) as count')
                ->from($prefix . 'visitor_log')
                ->where('country = ?', $country)
                ->group('ip')
                ->order('count', Typecho_Db::SORT_DESC)
                ->limit(5));
                
            $ipStats[$country] = $ips;
        }
        
        return array(
            'countries' => $countryStats,
            'ips' => $ipStats
        );
    }
} 