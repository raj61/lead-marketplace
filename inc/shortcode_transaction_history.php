<?php

  add_shortcode('transaction_history','transaction_history');
  function transaction_history($atts,$content = null)
  {
     global $wpdb;
     $url = plugins_url('',__FILE__);
     $url = str_replace('inc','frontend/css/lead-market-place-frontend.css',$url);
    ?>
<head>
  <meta charset="UTF-8">

      <link rel="stylesheet" href="<?php echo $url; ?>">

</head>

<body class = "timeline_class_shortcode">
  <section class="intro_class">
  <div class="container_class">
    <h1 class="heading_class">Transaction History &darr;</h1>
  </div>
</section>

<section class="timeline">
  <ul>
    <?php
      $current_user_id = get_current_user_id();
    $table_name = $wpdb->prefix . 'edugorilla_lead_client_mapping';
      $sql = "SELECT * FROM $table_name WHERE client_id = $current_user_id";
      $totalrows = $wpdb->get_results($sql);
      if(count($totalrows)>0){
        foreach($totalrows as $row){
          $new_time = explode(" ",$row->date_time);
          $get_date = $new_time[0];
          $get_time = $new_time[1]; ?>
          <li>
            <div>
              <time><?php echo $row->lead_id; ?><date class="date"><?php echo $get_date;?><tl class="tl"><?php echo $get_time;?></tl></date></time>
            </div>
          </li>
          <?php }
        }
        else{?>
              <li>
                <div>
                <time><h3>nothing to show<h3></time>
              </div>
            </li>

          <?php } ?>
  </ul>
</section>

</body>

<?php } ?>
