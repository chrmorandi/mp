 <?php
$columns=[];
foreach ($monthyearStats as $key => $val) {
  $columns[]=[$key,intval($val)];
}
   $chart =[
    'options' => [
            'id' => 'popularity_chart'
    ],
    'clientOptions' => [
      'data' => [
        'columns' => $columns,
          'type' => 'pie',
          'onclick' => 'function (d, i) { console.log("onclick", d, i); }',
          'onmouseover'=> 'function (d, i) { console.log("onmouseover", d, i); }',
          'onmouseout'=> 'function (d, i) { console.log("onmouseout", d, i); }',
          ]
        ]
      ];
      echo \yii2mod\c3\chart\Chart::widget(
        $chart
      );
?>
