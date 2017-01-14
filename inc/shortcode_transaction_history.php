<?php

  add_shortcode('transaction_history','transaction_history');
  function transaction_history($atts,$content = null)
  {
     $months = array("January","February","March","April","May","June","July","August","September","October","November","December");
     global $wpdb;
     $url = plugins_url('',__FILE__);
     $url = str_replace('inc','frontend/css/lead-market-place-frontend.css',$url);
     function script1()
     {
     	 wp_enqueue_style('select2-css', $url);
     }
     add_action('admin_enqueue_scripts', 'script1', 2000);

     $current_educash = 0;

     $current_user_id = get_current_user_id();
     $table_name1 = $wpdb->prefix . 'educash_transaction_history';
     $sql = "SELECT * FROM $table_name1 WHERE client_id = $current_user_id order by date_time";
     $totalrows = $wpdb->get_results($sql);

     $table_name2 = $wpdb->prefix . 'educash_deals';
     $sql = "SELECT transaction FROM $table_name2 WHERE client_id = $current_user_id";
     $total_cash = $wpdb->get_results($sql);

     if(count($total_cash)>0)
     {
       foreach ($total_cash as $cash)
       {
          if($cash->transaction > 0){
          $current_educash = $current_educash + ($cash->transaction);}
       }
     }

     $current_educash = $current_educash - count($totalrows);
     if($current_educash<0)
        $current_educash = 0;

    ?>

      <link rel="stylesheet" href="<?php //echo $url; ?>">

<div class = "timeline_class">
  <section class="intro_class">
  <div class="container_class">
    <h1 class="heading_class">Transaction History &darr;</h1>
    <h3 class="heading_class">(Your current educash is = <?php echo $current_educash; ?> )</h3>
  </div>
</section>

<section class="timeline">
  <ul>
    <?php

      if(count($totalrows)>0){
        foreach($totalrows as $row){
          $new_time = explode(" ",$row->date_time);
          $get_date = $new_time[0];
          $get_time = $new_time[1];
          $new_date = explode("-",$get_date);
          $year = $new_date[0];
          $month = $new_date[1];
          $date = $new_date[2];

          if (isset($monthly_consumption[$year][$month]))
            $monthly_consumption[$year][$month]=$monthly_consumption[$year][$month]+1;
          else{
            $monthly_consumption[$year][$month]=1;
          }
        }
        foreach ($monthly_consumption as $key => $value) {
          $current_year = $key;
          foreach ($value as $key1 => $val1) {
              $current_month = $key1;
              $current_count = $val1;
              ?>
              <li>
                <div>
                  <time><tl class="tl">You</tl><tl class="tl">spend</tl><tl class="tl"><?php echo $current_count." educash";?><tl class="tl">on</tl><tl class="tl"><?php echo$months[$current_month-1]; ?></tl><tl class="tl"><?php echo $current_year;?></tl></date></time>
                </div>
              </li>
              <?php
            }
          }
        }
        else{ }
        ?>
  </ul>
</section>

</div>

<?php } ?>
