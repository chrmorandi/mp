 <?php
$xAxis=['x'];
$yAxis=['predictions'];
//var_dump($dayStats);exit;
foreach ($dayStats as $key => $val) {
  $xAxis[]=Yii::$app->formatter->asDatetime($key,'Y-M-d');
  $yAxis[]=intval($val);
}
   $chart =[
    'clientOptions' => [
      'data' => [
        'x'=>'x',
        'columns' => [
          $xAxis,
          $yAxis,
        ],
        ],
        'axis' => [
            'x' => [
                'type'=>'timeseries',
                'tick'=> [
                  'format'=>'%Y-%m-%d',
                  'rotate'=> '80',
                ],
                'width'=>30,
            ],
        ]
        ]
      ];
      //var_dump($chart);
      echo \yii2mod\c3\chart\Chart::widget(
        $chart
      );
      ?>
