@php
use Carbon\Carbon;
Carbon::setLocale('fr');
@endphp

@extends('mail.layout')


@section('content')
<style type="text/css">
    #outlook a {
      padding: 0;
    }

    body {
      margin: 0;
      padding: 0;
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
    }

    table,
    td {
      border-collapse: collapse;
      mso-table-lspace: 0pt;
      mso-table-rspace: 0pt;
    }

    img {
      border: 0;
      height: auto;
      line-height: 100%;
      outline: none;
      text-decoration: none;
      -ms-interpolation-mode: bicubic;
    }

    p {
      display: block;
      margin: 13px 0;
    }
  </style><!--[if mso]>
    <noscript>
    <xml>
    <o:OfficeDocumentSettings>
      <o:AllowPNG/>
      <o:PixelsPerInch>96</o:PixelsPerInch>
    </o:OfficeDocumentSettings>
    </xml>
    </noscript>
    <![endif]--><!--[if lte mso 11]>
    <style type="text/css">
      .mj-outlook-group-fix { width:100% !important; }
    </style>
    <![endif]--><!--[if !mso]><!-->
  <link href="https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700" rel="stylesheet" type="text/css">
  <style type="text/css">
    @import url(https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700);
  </style><!--<![endif]-->
  <style type="text/css">
    @media only screen and (min-width:480px) {
      .mj-column-per-100 {
        width: 100% !important;
        max-width: 100%;
      }

      .mj-column-per-25 {
        width: 25% !important;
        max-width: 25%;
      }

      .mj-column-per-75 {
        width: 75% !important;
        max-width: 75%;
      }
    }
  </style>
  <style media="screen and (min-width:480px)">
    .moz-text-html .mj-column-per-100 {
      width: 100% !important;
      max-width: 100%;
    }

    .moz-text-html .mj-column-per-25 {
      width: 25% !important;
      max-width: 25%;
    }

    .moz-text-html .mj-column-per-75 {
      width: 75% !important;
      max-width: 75%;
    }
  </style>
  <style type="text/css">
    [owa] .mj-column-per-100 {
      width: 100% !important;
      max-width: 100%;
    }

    [owa] .mj-column-per-25 {
      width: 25% !important;
      max-width: 25%;
    }

    [owa] .mj-column-per-75 {
      width: 75% !important;
      max-width: 75%;
    }
  </style>
  <style type="text/css">
    @media only screen and (max-width:479px) {
      table.mj-full-width-mobile {
        width: 100% !important;
      }

      td.mj-full-width-mobile {
        width: auto !important;
      }
    }
  </style>
  <style type="text/css"></style>  
  <div style="background-color:#EBEBEB;">
    <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" bgcolor="#1D9CC4" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
    <div style="background:#1D9CC4;background-color:#1D9CC4;margin:0px auto;max-width:600px;">
      <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
        style="background:#1D9CC4;background-color:#1D9CC4;width:100%;">
        <tbody>
          <tr>
            <td
              style="direction:ltr;font-size:0px;padding:0px 0px 0px 0px;padding-bottom:0px;padding-left:0px;padding-right:0px;padding-top:0px;text-align:center;">
              <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
              <div class="mj-column-per-100 mj-outlook-group-fix"
                style="font-size:0;line-height:0;text-align:left;display:inline-block;width:100%;direction:ltr;vertical-align:top;">
                <!--[if mso | IE]><table border="0" cellpadding="0" cellspacing="0" role="presentation" ><tr><td style="vertical-align:top;width:150px;" ><![endif]-->
                <div class="mj-column-per-25 mj-outlook-group-fix"
                  style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:25%;">
                  <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;"
                    width="100%">
                    <tbody>
                      <tr>
                        <td align="center"
                          style="font-size:0px;padding:0px 0px 0px 0px;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px;word-break:break-word;">
                          <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                            style="border-collapse:collapse;border-spacing:0px;">
                            <tbody>
                              <tr>
                                <td style="width:150px;"><a href="https://www.lesplaisirsdeleau.fr/"
                                    target="_blank"><img alt="Les Plaisirs de l'Eau"
                                      src="http://xmmzr.mjt.lu/img/xmmzr/b/7q/20k.png"
                                      style="border:none;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;"
                                      width="150" height="auto"></a></td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div><!--[if mso | IE]></td><td style="vertical-align:top;width:450px;" ><![endif]-->
                <div class="mj-column-per-75 mj-outlook-group-fix"
                  style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:75%;">
                  <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;"
                    width="100%">
                    <tbody>
                      <tr>
                        <td align="center"
                          style="font-size:0px;padding:0px 0px 0px 0px;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px;word-break:break-word;">
                          <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                            style="border-collapse:collapse;border-spacing:0px;">
                            <tbody>
                              <tr>
                                <td style="width:450px;"><a href="https://www.lesplaisirsdeleau.fr/"
                                    target="_blank"><img alt="Les plaisirs de l'Eau"
                                      src="https://xmmzr.mjt.lu/tplimg/xmmzr/b/xrtyl/vnj37.png"
                                      style="border:none;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;"
                                      width="450" height="auto"></a></td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div><!--[if mso | IE]></td></tr></table><![endif]-->
              </div><!--[if mso | IE]></td></tr></table><![endif]-->
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" bgcolor="#ffffff" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
    <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:600px;">
      <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
        style="background:#ffffff;background-color:#ffffff;width:100%;">
        <tbody>
          <tr>
            <td
              style="direction:ltr;font-size:0px;padding:0px 0px 10px 0px;padding-bottom:10px;padding-left:0px;padding-right:0px;padding-top:0px;text-align:center;">
              <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
              <div class="mj-column-per-100 mj-outlook-group-fix"
                style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;"
                  width="100%">
                  <tbody>
                    <tr>
                      <td align="left"
                        style="font-size:0px;padding:10px 5px 5px 5px;padding-top:10px;padding-right:5px;padding-bottom:5px;padding-left:5px;word-break:break-word;">
                        <div
                          style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:1;text-align:left;color:#000000;">
                          <p class="text-build-content" style="text-align: center; margin: 10px 0; margin-top: 10px;"
                            data-testid="KtWjoB-VjqyjkXDsekX8O"><span
                              style="color:#0c5d89;font-family:Verdana;font-size:20px;line-height:20px;"><b>Souscription!</b></span>
                          </p>
                          <p class="text-build-content" style="text-align: center; margin: 10px 0;"
                            data-testid="KtWjoB-VjqyjkXDsekX8O"><span
                              style="color:#0c5d89;font-family:Verdana;font-size:20px;line-height:20px;"><b>&nbsp;
                                Bonjour,</b></span><br><span
                              style="color:#0c5d89;font-family:Verdana;font-size:20px;line-height:20px;"><b>Votre
                                souscription a bien été renouvelée pour:</b></span><br>&nbsp;</p>
                                @foreach ($wishes as $wishe)
        <li> {{ strtoupper($wishe['activity']) }}: {{ Carbon::parse($wishe['start'])->format('l d F Y') }}: {{ $wishe['time_start'] }} à {{ $wishe['time_end'] }}</li>
    @endforeach
                          <p class="text-build-content" style="text-align: center; margin: 10px 0; margin-bottom: 10px;"
                            data-testid="KtWjoB-VjqyjkXDsekX8O">&nbsp;</p>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div><!--[if mso | IE]></td></tr></table><![endif]-->
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" bgcolor="#1d9cc4" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
    <div style="background:#1d9cc4;background-color:#1d9cc4;margin:0px auto;max-width:600px;">
      <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
        style="background:#1d9cc4;background-color:#1d9cc4;width:100%;">
        <tbody>
          <tr>
            <td
              style="direction:ltr;font-size:0px;padding:20px 0px 20px 0px;padding-bottom:20px;padding-left:0px;padding-right:0px;padding-top:20px;text-align:center;">
              <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
              <div class="mj-column-per-100 mj-outlook-group-fix"
                style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;"
                  width="100%">
                  <tbody>
                    <tr>
                      <td align="left"
                        style="font-size:0px;padding:10px 25px;padding-top:0px;padding-bottom:0px;word-break:break-word;">
                        <div
                          style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:1;text-align:left;color:#000000;">
                          <p style="text-align: center; margin: 10px 0; margin-top: 10px;"><span
                              style="line-height:20px;text-align:left;letter-spacing:normal;font-size:16px;font-family:Verdana;color:#ffffff;text-align:left;"><b>A
                                très bientôt,</b></span></p>
                          <p style="text-align: center; margin: 10px 0; margin-bottom: 10px;"><span
                              style="line-height:20px;text-align:left;letter-spacing:normal;font-size:16px;font-family:Verdana;color:#ffffff;text-align:left;"><b>L’équipe
                                des Plaisirs de l’eau</b></span></p>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td align="center" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                          style="border-collapse:collapse;border-spacing:0px;">
                          <tbody>
                            <tr>
                              <td style="width:53px;"><img alt="" src="http://xmmzr.mjt.lu/img/xmmzr/b/7q/2x6.png"
                                  style="border:none;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;"
                                  width="53" height="auto"></td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div><!--[if mso | IE]></td></tr></table><![endif]-->
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" bgcolor="#1d9cc4" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
    <div style="background:#1d9cc4;background-color:#1d9cc4;margin:0px auto;max-width:600px;">
      <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
        style="background:#1d9cc4;background-color:#1d9cc4;width:100%;">
        <tbody>
          <tr>
            <td
              style="direction:ltr;font-size:0px;padding:20px 50px 20px 50px;padding-bottom:20px;padding-left:50px;padding-right:50px;padding-top:20px;text-align:center;">
              <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:500px;" ><![endif]-->
              <div class="mj-column-per-100 mj-outlook-group-fix"
                style="font-size:0;line-height:0;text-align:left;display:inline-block;width:100%;direction:ltr;vertical-align:top;">
                <!--[if mso | IE]><table border="0" cellpadding="0" cellspacing="0" role="presentation" ><tr><td style="vertical-align:top;width:125px;" ><![endif]-->
                <div class="mj-column-per-25 mj-outlook-group-fix"
                  style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:25%;">
                  <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;"
                    width="100%">
                    <tbody>
                      <tr>
                        <td align="center" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                          <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                            style="border-collapse:collapse;border-spacing:0px;">
                            <tbody>
                              <tr>
                                <td style="width:37px;"><a href="https://www.instagram.com/lesplaisirsdeleau/"
                                    target="_blank"><img alt="Suivez-nous sur Instagram !"
                                      src="http://xmmzr.mjt.lu/img/xmmzr/b/7q/2xh.png"
                                      style="border:none;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;"
                                      width="37" height="auto"></a></td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div><!--[if mso | IE]></td><td style="vertical-align:top;width:125px;" ><![endif]-->
                <div class="mj-column-per-25 mj-outlook-group-fix"
                  style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:25%;">
                  <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;"
                    width="100%">
                    <tbody>
                      <tr>
                        <td align="center" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                          <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                            style="border-collapse:collapse;border-spacing:0px;">
                            <tbody>
                              <tr>
                                <td style="width:37px;"><a
                                    href="http://www.facebook.com/pages/Les-Plaisirs-de-lEau/289950444398639"
                                    target="_blank"><img alt="Suivez-nous sur Facebook !"
                                      src="http://xmmzr.mjt.lu/img/xmmzr/b/7q/20p.png"
                                      style="border:none;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;"
                                      width="37" height="auto"></a></td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div><!--[if mso | IE]></td><td style="vertical-align:top;width:125px;" ><![endif]-->
                <div class="mj-column-per-25 mj-outlook-group-fix"
                  style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:25%;">
                  <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;"
                    width="100%">
                    <tbody>
                      <tr>
                        <td align="center" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                          <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                            style="border-collapse:collapse;border-spacing:0px;">
                            <tbody>
                              <tr>
                                <td style="width:31px;"><a
                                    href="https://www.youtube.com/channel/UC-7fddgLq6BxrSIYGD72Rtw" target="_blank"><img
                                      alt="Visionnez nos vidées sur Youtube !"
                                      src="http://xmmzr.mjt.lu/img/xmmzr/b/7q/204.png"
                                      style="border:none;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;"
                                      width="31" height="auto"></a></td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div><!--[if mso | IE]></td><td style="vertical-align:top;width:125px;" ><![endif]-->
                <div class="mj-column-per-25 mj-outlook-group-fix"
                  style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:25%;">
                  <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;"
                    width="100%">
                    <tbody>
                      <tr>
                        <td align="center" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                          <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                            style="border-collapse:collapse;border-spacing:0px;">
                            <tbody>
                              <tr>
                                <td style="width:25px;"><a href="https://www.lesplaisirsdeleau.fr/plan-dacces/"
                                    target="_blank"><img alt="Où nous trouver"
                                      src="http://xmmzr.mjt.lu/img/xmmzr/b/7q/205.png"
                                      style="border:none;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;"
                                      width="25" height="auto"></a></td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div><!--[if mso | IE]></td></tr></table><![endif]-->
              </div><!--[if mso | IE]></td></tr></table><![endif]-->
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" bgcolor="#1d9cc4" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
    <div style="background:#1d9cc4;background-color:#1d9cc4;margin:0px auto;max-width:600px;">
      <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
        style="background:#1d9cc4;background-color:#1d9cc4;width:100%;">
        <tbody>
          <tr>
            <td
              style="direction:ltr;font-size:0px;padding:20px 0px 20px 0px;padding-bottom:20px;padding-left:0px;padding-right:0px;padding-top:20px;text-align:center;">
              <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
              <div class="mj-column-per-100 mj-outlook-group-fix"
                style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;"
                  width="100%">
                  <tbody>
                    <tr>
                      <td align="left"
                        style="font-size:0px;padding:10px 25px;padding-top:0px;padding-bottom:0px;word-break:break-word;">
                        <div
                          style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:1;text-align:left;color:#000000;">
                          <p style="text-align: center; margin: 10px 0; margin-top: 10px; margin-bottom: 10px;"><span
                              style="line-height:20px;text-align:center;letter-spacing:normal;font-size:12px;font-family:Verdana;color:#ffffff;text-align:left;"><b>Tél
                                : </b></span><a href="tel:0556969513" style="; text-decoration: none;"><span><b><span
                                    style="line-height:20px;background-color:transparent;text-align:center;letter-spacing:normal;font-size:12px;font-family:Verdana;color:#ffffff;text-align:left;"><b><u>05
                                        56 96 95 13</u></b></span><u><span
                                      style="line-height:20px;background-color:transparent;text-align:center;letter-spacing:normal;font-size:12px;font-family:Verdana;color:#ffffff;text-align:left;"><b><u>
                                        </u></b></span></u></b></span></a><span
                              style="line-height:20px;text-align:center;letter-spacing:normal;font-size:12px;font-family:Verdana;color:#ffffff;text-align:left;"><b>-
                              </b></span><a href="mailto:contact@lesplaisirsdeleau.fr"
                              style="; text-decoration: none;"><span><b><u><span
                                      style="line-height:20px;background-color:transparent;text-align:center;letter-spacing:normal;font-size:12px;font-family:Verdana;color:#ffffff;text-align:left;"><b><u>contact@lesplaisirsdeleau.fr</u></b></span></u></b></span></a><span
                              style="text-align:center;letter-spacing:normal;font-size:16px;font-family:Arial;color:#000000;text-align:left;"><br></span><a
                              href="https://www.google.com/search?rlz=1C5CHFA_enFR709FR709&q=les+plaisirs+de+l%27eau&npsic=0&rflfq=1&rlha=0&rllag=44852514,-635281,2291&tbm=lcl&ved=2ahUKEwi8numS3J3jAhXRA2MBHV4zCk4QtgN6BAgHEAQ&tbs=lrf:!2m1!1e2!2m1!1e3!3sIAE,lf:1,lf_ui:2&rldoc=1#rlfi=hd:;si:14529512429235833993;mv:!1m2!1d44.85802262482371!2d-0.566187695776307!2m2!1d44.82436735304528!2d-0.6537349980224008!4m2!1d44.84119744639277!2d-0.6099613468993539!5i14"
                              target="_blank" style="; text-decoration: none;"><span><span
                                  style="line-height:20px;text-align:center;letter-spacing:normal;font-size:12px;font-family:Verdana;color:#ffffff;text-align:left;"><u>267,
                                    avenue d’Arès - 33200 BX CAUDERAN/MERIGNAC</u></span></span></a><span
                              style="text-align:center;letter-spacing:normal;font-size:16px;font-family:Arial;color:#000000;text-align:left;"><br></span><a
                              href="https://www.google.com/search?rlz=1C5CHFA_enFR709FR709&ei=dzofXZz-O9aBjLsP3Kq1wAc&q=les%20plaisirs%20de%20l%27eau&oq=les+plaisirs+de+l%27eau&gs_l=psy-ab.3..0l7.2918.6451..6710...0.0..0.268.1636.10j5j1......0....1..gws-wiz.......0i131.kuFHCQtk9ko&npsic=0&rflfq=1&rlha=0&rllag=44852514,-635281,2291&tbm=lcl&rldimm=17263360153873795666&ved=2ahUKEwi8numS3J3jAhXRA2MBHV4zCk4QvS4wAXoECAcQKg&rldoc=1&tbs=lrf:!2m1!1e2!2m1!1e3!3sIAE,lf:1,lf_ui:2#rlfi=hd:;si:17263360153873795666;mv:!1m2!1d44.8682166!2d-0.6113936999999999!2m2!1d44.836811499999996!2d-0.6591691!3m12!1m3!1d15262.008380137335!2d-0.6352813999999999!3d44.852514049999996!2m3!1f0!2f0!3f0!3m2!1i279!2i259!4f13.1;tbs:lrf:!2m1!1e2!2m1!1e3!3sIAE,lf:1,lf_ui:2"
                              target="_blank" style="; text-decoration: none;"><span><span
                                  style="line-height:20px;text-align:center;letter-spacing:normal;font-size:12px;font-family:Verdana;color:#ffffff;text-align:left;"><u>Z.A.
                                    MERMOZ - 9 avenue de la Forêt - 33320 EYSINES</u></span></span></a><span
                              style="text-align:center;letter-spacing:normal;font-size:16px;font-family:Arial;color:#000000;text-align:left;"><br></span><a
                              href="https://www.lesplaisirsdeleau.fr" target="_blank"
                              style="; text-decoration: none;"><span><b><u><span
                                      style="line-height:20px;background-color:transparent;text-align:center;letter-spacing:normal;font-size:20px;font-family:Verdana;color:#ffffff;text-align:left;"><b><u>lesplaisirsdeleau.fr</u></b></span></u></b></span></a>
                          </p>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div><!--[if mso | IE]></td></tr></table><![endif]-->
            </td>
          </tr>
        </tbody>
      </table>
    </div><!--[if mso | IE]></td></tr></table><![endif]-->
  </div> 
@endsection
