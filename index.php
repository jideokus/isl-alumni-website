<?php
    if(isset($_POST['sp_token'])){
      function successAction(){
        global $guest_1,$guest_2,$transaction_id,$ticket_type;
        require_once('emails.php');
        $ticket_type_text="";
        if($ticket_type=='single_ticket'){
          $ticket_type_text='Single Ticket';
        }else if ($ticket_type=='couples_ticket'){
          $ticket_type_text='Couples Ticket';
        }
        $notification_email = getNotificationEmail($guest_1,$guest_2,$ticket_type_text,$transaction_id);
        $headers = array();
        array_push($headers, 'MIME-Version: 1.0');
        array_push($headers, 'Content-type: text/html; charset=iso-8859-1');
        array_push($headers, 'To: ISL Class of 07 Planning Committee <islclassof07@gmail.com>');
        array_push($headers, 'From: ISL Class of 07 Website <no-reply@islclassof07.org>');
        mail ( 'islclassof07@gmail.com' , 'New RSVP for Event - '.$transaction_id , $notification_email,implode("\r\n", $headers));
        header('Location: thank-you.html');
        die();
      }
      $private_key = 'test_pr_8780c7d038ec4473802010c92f96f18f';
      $token = $_POST["sp_token"];
      $ticket_type = $_POST["ticket_type"];
      $amount = 0;
      $is_couples_ticket = false;
      if($ticket_type=='single_ticket'){
        $amount = 1000000;
      }else if($ticket_type=='couples_ticket'){
        $amount = 2000000;
        $is_couples_ticket = true;
      }
      $guest_1 = array(
        'first_name'=>$_POST['guest_1_first_name'],
        'last_name'=>$_POST['guest_1_last_name'],
        'email'=>$_POST['guest_1_email'],
        'phone_number'=>$_POST['guest_1_phone_number'],
      );
      $guest_2 = array();
      if($is_couples_ticket){
        $guest_2 = array(
          'first_name'=>$_POST['guest_2_first_name'],
          'last_name'=>$_POST['guest_2_last_name'],
          'email'=>$_POST['guest_2_email'],
          'phone_number'=>$_POST['guest_2_phone_number'],
        );
      }
      $amount_currency = 'NGN';
      $data = array(
        'token' => $token,
        'amount' => $amount,
        'amount_currency' => $amount_currency
      );
      $data_string = json_encode($data);
      // Call to charge/verify a payment
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, 'https://checkout.simplepay.ng/v2/payments/card/charge/');
      curl_setopt($ch, CURLOPT_USERPWD, $private_key . ':');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
      curl_setopt($ch, CURLOPT_HEADER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json',
          'Content-Length: ' . strlen($data_string)
      ));
      $curl_response = curl_exec($ch);
      $curl_response = preg_split("/\r\n\r\n/", $curl_response);
      $response_content = $curl_response[1];
      $json_response = json_decode(chop($response_content), TRUE);
      $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);
      $transaction_id = $json_response['id'];
      //Modal flags
      $charge_failed = false;
      if ($response_code == '200') {
      // even is http status code is 200 we still need to check transaction had issues or not
        if ($json_response['response_code'] == '20000') {
            // card was successfully charged
            successAction();
        } else {
            // failed to charge the card
            $charge_failed = true;
        }
      } else if ($sp_status == 'true') {
          // even though it failed the call to card charge, card payment was already processed
          successAction();
      } else {
          // failed to charge the card
          $charge_failed = true;
      }
      successAction();

    }
   ?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ISL Class of 07 - Reunion RSVP</title>

    <!-- CSS  -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection" />
    <link href="css/style.css" type="text/css" rel="stylesheet" media="screen,projection" />
  </head>

  <body>

    <div id="index-banner" class="parallax-container">
      <div class="section no-pad-bot">
        <div class="container">
          <br><br>
          <h1 class="header center isl-green-text">ISL Class of 07 Reunion</h1>
          <div class="row center">
            <h5 class="header col s12 light">After 10 years, getting together once more to connect and celebrate would be a worthwhile experience</h5>
          </div>
          <div class="row center">
            <a href="#" id="rsvp-now-button" class="btn-large waves-effect waves-light isl-green">RSVP Now <i class="fa fa-calendar-check-o" aria-hidden="true"></i></a>
          </div>
          <br><br>

        </div>
      </div>
      <div class="parallax"><img src="./images/banner-background.jpg" alt="Unsplashed background img 1"></div>
    </div>


    <div class="container">
      <div class="section">

        <!--   Icon Section   -->
        <div class="row">
          <div class="col s12 m4">
            <div class="icon-block">
              <h2 class="center isl-green-text"><i class="fa fa-calendar" aria-hidden="true"></i></h2>
              <h5 class="center">Date</h5>
              <p class="light center">21st December, 2017</p>
            </div>
          </div>

          <div class="col s12 m4">
            <div class="icon-block">
              <h2 class="center isl-green-text"><i class="fa fa-clock-o" aria-hidden="true"></i></h2>
              <h5 class="center">Time</h5>
              <p class="light center">TBA</p>
            </div>
          </div>

          <div class="col s12 m4">
            <div class="icon-block">
              <h2 class="center isl-green-text"><i class="fa fa-map-marker" aria-hidden="true"></i></h2>
              <h5 class="center">Venue</h5>

              <p class="light center"><a href="https://goo.gl/maps/vz5HnLMZcsF2">Oriental Hotel Eti-Osa Lagos | 3 Lekki - Epe Express Way | Lagos | Nigeria</a></p>
            </div>
          </div>
        </div>

      </div>
    </div>


    <div class="parallax-container valign-wrapper isl-green-dark">
      <div class="section no-pad-bot">
        <div class="container">
          <div class="row center">
            <h5 class="header col s12 light">RSVP now to save your spot at the reunion</h5>
          </div>
        </div>
      </div>

    </div>
    <div class="container" id="rsvp_section">
      <div class="section">
        <div class="row center">
          <div class="col s12 m6 offset-m3">
            <form method="post" action="#" name="reunion_rsvp_form" id="reunion_rsvp_form">
              <div class="row">
                <div class="col s12">
                  <h5>Ticket Type</h5>
                </div>
              </div>
              <div class="row">
                <div class="col s6">
                  <input class="with-gap" name="ticket_type" type="radio" id="single_ticket_type" value="single_ticket" />
                  <label for="single_ticket_type">Single Ticket - ₦10,000</label>
                </div>
                <div class="col s6">
                  <input class="with-gap" name="ticket_type" type="radio" id="couple_ticket_type" value="couples_ticket" />
                  <label for="couple_ticket_type">Couple's Ticket - ₦20,000</label>
                </div>
              </div>
              <div class="row">
                <div class="col s12">
                  <h5>Main Attendee Information</h5>
                </div>
              </div>
              <div class="row">
                <div class="col s12 m6 input-field">
                  <input id="guest_1_first_name" name="guest_1_first_name" type="text" class="validate">
                  <label for="guest_1_first_name">First Name</label>
                </div>
                <div class="col s12 m6 input-field">
                  <input id="guest_1_last_name" name="guest_1_last_name" type="text" class="validate">
                  <label for="guest_1_last_name">Last Name</label>
                </div>
              </div>
              <div class="row">
                <div class="col s12 m6 input-field">
                  <input id="guest_1_email" name="guest_1_email" type="text" class="validate">
                  <label for="guest_1_email">Email</label>
                </div>
                <div class="col s12 m6 input-field">
                  <input id="guest_1_phone_number" name="guest_1_phone_number" type="text" class="validate">
                  <label for="guest_1_phone_number">Phone Number</label>
                </div>
              </div>
              <div class="row">
                <div class="col s12">
                  <h5>Guest Information</h5>
                </div>
              </div>
              <div class="row">
                <div class="col s12 m6 input-field">
                  <input id="guest_2_first_name" name="guest_2_first_name" type="text" class="validate">
                  <label for="guest_2_first_name">First Name</label>
                </div>
                <div class="col s12 m6 input-field">
                  <input id="guest_2_last_name" name="guest_2_last_name" type="text" class="validate">
                  <label for="guest_2_last_name">Last Name</label>
                </div>
              </div>
              <div class="row">
                <div class="col s12 m6 input-field">
                  <input id="guest_2_email" name="guest_2_email" type="text" class="validate">
                  <label for="guest_2_email">Email</label>
                </div>
                <div class="col s12 m6 input-field">
                  <input id="guest_2_phone_number" name="guest_2_phone_number" type="text" class="validate">
                  <label for="guest_2_phone_number">Phone Number</label>
                </div>
              </div>
              <div class="row">
                <div class="col s12">
                  <h5>Billing Information</h5>
                </div>
              </div>
              <div class="row">
                <div class="col s12 m6 input-field">
                  <input id="billing_info_address" name="billing_info_address" type="text" class="validate">
                  <label for="billing_info_address">Billing Address</label>
                </div>
                <div class="col s12 m6 input-field">
                  <input id="billing_info_city" name="billing_info_city" type="text" class="validate">
                  <label for="billing_info_city">City</label>
                </div>
              </div>
              <div class="row">
                <div class="col s12 m6 input-field">
                  <input id="billing_info_postal_code" name="billing_info_postal_code" type="text" class="validate">
                  <label for="billing_info_postal_code">Postal Code</label>
                </div>
                <div class="col s12 m6 input-field">
                  <select id="billing_info_country" name="billing_info_country">
                  </select>
                  <label for="billing_info_country">Country</label>
                </div>
              </div>
              <div class="row">
                <div class="col s12">
                  <input class="btn isl-green" type="submit" value="Complete RSVP" id="rsvp_complete_button">
                </div>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>

    <footer class="page-footer isl-green-dark">
      <div class="footer-copyright">
        <div class="container">
          Made by <a class="brown-text text-lighten-3" href="http://islclassof07.org">ISL Class of 07 Planning Committee</a>
        </div>
      </div>
    </footer>

     <?php
      if($charge_failed):?>
        <div id="failed_message_modal" class="modal">
           <div class="modal-content">
             <h4 class="red-text">Transation Failed</h4>
             <p>There was a problem processing your payment. Please try again</p>
           </div>
           <div class="modal-footer">
             <a href="#" class="modal-action modal-close waves-effect waves-green btn-flat">Ok</a>
           </div>
        </div>
        <script>
          var charge_failed = true;
        </script>
      <?php endif;
     ?>


    <!--  Scripts-->
    <script src="https://checkout.simplepay.ng/v2/simplepay.js"></script>
    <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="https://use.fontawesome.com/787e563b54.js"></script>
    <script src="js/materialize.js"></script>
    <script src="js/init.js"></script>
  </body>

  </html>
