<?php
/**
 * 访客统计后台面板
 * 
 * @package VisitorLogger
 * @author BXCQ
 * @version 1.0.0
 * @link https://blog.ybyq.wang/
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 后台面板类
 */
class VisitorLogger_Panel
{
    /**
     * 显示后台面板
     * 
     * @access public
     * @return void
     */
    public static function render()
    {
        // 获取统计数据
        $stats = VisitorLogger_Statistic::getVisitStatistic();
        
        // 显示面板
        ?>
        <div class="visitor-stats-panel">
            <h2>访问统计</h2>
            
            <!-- 日期筛选 -->
            <div class="date-filter">
                <input type="date" id="startDate">
                <span>至</span>
                <input type="date" id="endDate">
                <button id="filterBtn">筛选</button>
                <button id="resetBtn">重置</button>
            </div>
            
            <!-- 统计图表 -->
            <div id="statsChart"></div>
            
            <!-- 统计列表 -->
            <div id="statsList">
                <table>
                    <thead>
                        <tr>
                            <th>国家/地区</th>
                            <th>访问次数</th>
                            <th>占比</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['countries'] as $stat): ?>
                        <tr>
                            <td><?php echo $stat['country']; ?></td>
                            <td><?php echo $stat['count']; ?></td>
                            <td><?php echo number_format($stat['count'] / array_sum(array_column($stats['countries'], 'count')) * 100, 2); ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <script>
        // 初始化图表
        var chart = echarts.init(document.getElementById('statsChart'));
        
        // 图表配置
        var option = {
            title: {
                text: '访问国家/地区统计'
            },
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b}: {c} ({d}%)'
            },
            legend: {
                orient: 'vertical',
                left: 10,
                data: <?php echo json_encode(array_column($stats['countries'], 'country')); ?>
            },
            series: [
                {
                    name: '访问次数',
                    type: 'pie',
                    radius: ['50%', '70%'],
                    avoidLabelOverlap: false,
                    label: {
                        show: false,
                        position: 'center'
                    },
                    emphasis: {
                        label: {
                            show: true,
                            fontSize: '30',
                            fontWeight: 'bold'
                        }
                    },
                    labelLine: {
                        show: false
                    },
                    data: <?php echo json_encode(array_map(function($item) {
                        return array(
                            'name' => $item['country'],
                            'value' => $item['count']
                        );
                    }, $stats['countries'])); ?>
                }
            ]
        };
        
        // 使用配置项显示图表
        chart.setOption(option);
        
        // 响应式调整
        window.addEventListener('resize', function() {
            chart.resize();
        });
        </script>
        <?php
    }
} 