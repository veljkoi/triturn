<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Triturn</title>
        <meta name="description" content="Online puzzle game of higher skill and strategy. Join 5 triangles into various geometric shapes and move them by turning them over their edges. Play now.">
        <meta name="author" content="">
        
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link href='//fonts.googleapis.com/css?family=Raleway:400,300,600' rel='stylesheet' type='text/css'>
        
        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/skeleton.css">
        <link rel="stylesheet" href="css/custom.css">
        
        <script src="js/jquery-1.11.3.min.js" type="text/javascript" charset="UTF-8"></script>
        <script src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js"></script>
        <link rel="stylesheet" href="css/github-prettify-theme.css">
        
        <link rel="icon" type="image/png" href="images/favicon-32x32.png" sizes="32x32">
        
        <!--[if lte IE 9]>
            <script>
                (function(f){
                    window.setTimeout =f(window.setTimeout);
                    window.setInterval =f(window.setInterval);
                })(function(f){return function(c,t){
                var a=[].slice.call(arguments,2);return f(function(){c.apply(this,a)},t)}
                });
            </script>
        <![endif]-->
        
        <script src="js/maps.js" type="text/javascript" charset="UTF-8"></script>
        <script src="js/triturn.js" type="text/javascript" charset="UTF-8"></script>
        
        <script>
            $(document).ready(function() {
                Util.initScroll();
                Demo.pointer = $('#pointer');
                
                <?php if ($anchor): ?>
                    Util.scroll('<?php echo $anchor; ?>', 1);
                <?php endif; ?>
            });
        </script>
        
        <?php if (!$loggedIn): ?>
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <?php endif; ?>
    </head>
    <body class="code-snippets-visible">
        
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
            
            ga('create', 'UA-65294826-1', 'auto');
            ga('send', 'pageview');
            
        </script>
        
        <div id="fb-root"></div>
        <script>(function(d, s, id) {
          var js, fjs = d.getElementsByTagName(s)[0];
          if (d.getElementById(id)) return;
          js = d.createElement(s); js.id = id;
          js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3&appId=961474663894011";
          fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>
        
        <div class="container">

            <section class="header">
                <h2 class="title"><img src="images/logo_simple.png" alt="Triturn" height="52" width="52">Triturn</h2>
            </section>

            <div class="navbar-spacer"></div>
            
            <nav class="navbar">
                <div class="container">
                    <ul class="navbar-list">
                        <li class="navbar-item"><a class="navbar-link anchor" href="#table">Table</a></li>
                        <?php if ($loggedIn): ?>
                            <li class="navbar-item"><a class="navbar-link anchor" href="#history">History</a></li>
                        <?php endif; ?>
                        <li class="navbar-item"><a class="navbar-link anchor" href="#standings">Standings</a></li>
                        <li class="navbar-item"><a class="navbar-link anchor" href="#login"><?php if ($loggedIn): ?>Profile<?php else: ?>Login<?php endif; ?></a></li>
                        <div class="fb-like" data-href="http://triturn.org/" data-layout="button" data-action="like" data-show-faces="true" data-share="true"></div>
                    </ul>
                </div>
            </nav>
            
            <div class="docs-section" id="table">
                
                <div class="dirs">
                    <div class="dir start"><span class="blink">SELECT 5 TRIANGLES</span> AND THEN CLICK ON SELECT BUTTON</div>
                    <div class="dir move"> >>> MOVE >>> </div>
                    <div class="dir comp"> <<< COMPUTER <<< </div>
                    <div class="dir end"></div>
                    <div class="message table hidden"></div>
                </div>
                
                <div class="demo-notes">
                    <div class="demo-note"></div>
                    <div class="note left"></div>
                    <div class="note right"></div>
                </div>
                
                <svg  width='720' height='300'>
                    <polygon class='field' id='1100' points='30,270 0,300 0,240' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='1101' points='30,270 60,300 60,240' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='1110' points='30,270 0,240 60,240' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='1111' points='30,270 0,300 60,300' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='1200' points='30,210 0,240 0,180' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='1201' points='30,210 60,240 60,180' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='1210' points='30,210 0,180 60,180' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='1211' points='30,210 0,240 60,240' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='1300' points='30,150 0,180 0,120' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='1301' points='30,150 60,180 60,120' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='1310' points='30,150 0,120 60,120' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='1311' points='30,150 0,180 60,180' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='1400' points='30,90 0,120 0,60' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='1401' points='30,90 60,120 60,60' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='1410' points='30,90 0,60 60,60' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='1411' points='30,90 0,120 60,120' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='1500' points='30,30 0,60 0,0' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='1501' points='30,30 60,60 60,0' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='1510' points='30,30 0,0 60,0' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='1511' points='30,30 0,60 60,60' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='2100' points='90,270 60,300 60,240' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='2101' points='90,270 120,300 120,240' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='2110' points='90,270 60,240 120,240' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='2111' points='90,270 60,300 120,300' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='2200' points='90,210 60,240 60,180' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='2201' points='90,210 120,240 120,180' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='2210' points='90,210 60,180 120,180' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='2211' points='90,210 60,240 120,240' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='2300' points='90,150 60,180 60,120' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='2301' points='90,150 120,180 120,120' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='2310' points='90,150 60,120 120,120' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='2311' points='90,150 60,180 120,180' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='2400' points='90,90 60,120 60,60' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='2401' points='90,90 120,120 120,60' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='2410' points='90,90 60,60 120,60' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='2411' points='90,90 60,120 120,120' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='2500' points='90,30 60,60 60,0' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='2501' points='90,30 120,60 120,0' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='2510' points='90,30 60,0 120,0' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='2511' points='90,30 60,60 120,60' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='3100' points='150,270 120,300 120,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='3101' points='150,270 180,300 180,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='3110' points='150,270 120,240 180,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='3111' points='150,270 120,300 180,300' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='3200' points='150,210 120,240 120,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='3201' points='150,210 180,240 180,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='3210' points='150,210 120,180 180,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='3211' points='150,210 120,240 180,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='3300' points='150,150 120,180 120,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='3301' points='150,150 180,180 180,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='3310' points='150,150 120,120 180,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='3311' points='150,150 120,180 180,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='3400' points='150,90 120,120 120,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='3401' points='150,90 180,120 180,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='3410' points='150,90 120,60 180,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='3411' points='150,90 120,120 180,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='3500' points='150,30 120,60 120,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='3501' points='150,30 180,60 180,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='3510' points='150,30 120,0 180,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='3511' points='150,30 120,60 180,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='4100' points='210,270 180,300 180,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='4101' points='210,270 240,300 240,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='4110' points='210,270 180,240 240,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='4111' points='210,270 180,300 240,300' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='4200' points='210,210 180,240 180,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='4201' points='210,210 240,240 240,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='4210' points='210,210 180,180 240,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='4211' points='210,210 180,240 240,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='4300' points='210,150 180,180 180,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='4301' points='210,150 240,180 240,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='4310' points='210,150 180,120 240,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='4311' points='210,150 180,180 240,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='4400' points='210,90 180,120 180,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='4401' points='210,90 240,120 240,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='4410' points='210,90 180,60 240,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='4411' points='210,90 180,120 240,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='4500' points='210,30 180,60 180,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='4501' points='210,30 240,60 240,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='4510' points='210,30 180,0 240,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='4511' points='210,30 180,60 240,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='5100' points='270,270 240,300 240,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='5101' points='270,270 300,300 300,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='5110' points='270,270 240,240 300,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='5111' points='270,270 240,300 300,300' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='5200' points='270,210 240,240 240,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='5201' points='270,210 300,240 300,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='5210' points='270,210 240,180 300,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='5211' points='270,210 240,240 300,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='5300' points='270,150 240,180 240,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='5301' points='270,150 300,180 300,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='5310' points='270,150 240,120 300,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='5311' points='270,150 240,180 300,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='5400' points='270,90 240,120 240,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='5401' points='270,90 300,120 300,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='5410' points='270,90 240,60 300,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='5411' points='270,90 240,120 300,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='5500' points='270,30 240,60 240,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='5501' points='270,30 300,60 300,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='5510' points='270,30 240,0 300,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='5511' points='270,30 240,60 300,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='6100' points='330,270 300,300 300,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='6101' points='330,270 360,300 360,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='6110' points='330,270 300,240 360,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='6111' points='330,270 300,300 360,300' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='6200' points='330,210 300,240 300,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='6201' points='330,210 360,240 360,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='6210' points='330,210 300,180 360,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='6211' points='330,210 300,240 360,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='6300' points='330,150 300,180 300,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='6301' points='330,150 360,180 360,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='6310' points='330,150 300,120 360,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='6311' points='330,150 300,180 360,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='6400' points='330,90 300,120 300,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='6401' points='330,90 360,120 360,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='6410' points='330,90 300,60 360,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='6411' points='330,90 300,120 360,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='6500' points='330,30 300,60 300,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='6501' points='330,30 360,60 360,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='6510' points='330,30 300,0 360,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='6511' points='330,30 300,60 360,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='7100' points='390,270 360,300 360,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='7101' points='390,270 420,300 420,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='7110' points='390,270 360,240 420,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='7111' points='390,270 360,300 420,300' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='7200' points='390,210 360,240 360,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='7201' points='390,210 420,240 420,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='7210' points='390,210 360,180 420,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='7211' points='390,210 360,240 420,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='7300' points='390,150 360,180 360,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='7301' points='390,150 420,180 420,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='7310' points='390,150 360,120 420,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='7311' points='390,150 360,180 420,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='7400' points='390,90 360,120 360,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='7401' points='390,90 420,120 420,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='7410' points='390,90 360,60 420,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='7411' points='390,90 360,120 420,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='7500' points='390,30 360,60 360,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='7501' points='390,30 420,60 420,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='7510' points='390,30 360,0 420,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='7511' points='390,30 360,60 420,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='8100' points='450,270 420,300 420,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='8101' points='450,270 480,300 480,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='8110' points='450,270 420,240 480,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='8111' points='450,270 420,300 480,300' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='8200' points='450,210 420,240 420,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='8201' points='450,210 480,240 480,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='8210' points='450,210 420,180 480,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='8211' points='450,210 420,240 480,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='8300' points='450,150 420,180 420,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='8301' points='450,150 480,180 480,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='8310' points='450,150 420,120 480,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='8311' points='450,150 420,180 480,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='8400' points='450,90 420,120 420,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='8401' points='450,90 480,120 480,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='8410' points='450,90 420,60 480,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='8411' points='450,90 420,120 480,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='8500' points='450,30 420,60 420,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='8501' points='450,30 480,60 480,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='8510' points='450,30 420,0 480,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='8511' points='450,30 420,60 480,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='9100' points='510,270 480,300 480,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='9101' points='510,270 540,300 540,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='9110' points='510,270 480,240 540,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='9111' points='510,270 480,300 540,300' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='9200' points='510,210 480,240 480,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='9201' points='510,210 540,240 540,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='9210' points='510,210 480,180 540,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='9211' points='510,210 480,240 540,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='9300' points='510,150 480,180 480,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='9301' points='510,150 540,180 540,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='9310' points='510,150 480,120 540,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='9311' points='510,150 480,180 540,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='9400' points='510,90 480,120 480,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='9401' points='510,90 540,120 540,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='9410' points='510,90 480,60 540,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='9411' points='510,90 480,120 540,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='9500' points='510,30 480,60 480,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='9501' points='510,30 540,60 540,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='9510' points='510,30 480,0 540,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='9511' points='510,30 480,60 540,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='10100' points='570,270 540,300 540,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='10101' points='570,270 600,300 600,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='10110' points='570,270 540,240 600,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='10111' points='570,270 540,300 600,300' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='10200' points='570,210 540,240 540,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='10201' points='570,210 600,240 600,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='10210' points='570,210 540,180 600,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='10211' points='570,210 540,240 600,240' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='10300' points='570,150 540,180 540,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='10301' points='570,150 600,180 600,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='10310' points='570,150 540,120 600,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='10311' points='570,150 540,180 600,180' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='10400' points='570,90 540,120 540,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='10401' points='570,90 600,120 600,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='10410' points='570,90 540,60 600,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='10411' points='570,90 540,120 600,120' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='10500' points='570,30 540,60 540,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='10501' points='570,30 600,60 600,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='10510' points='570,30 540,0 600,0' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='10511' points='570,30 540,60 600,60' style='fill:#EEEEEE;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='11100' points='630,270 600,300 600,240' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='11101' points='630,270 660,300 660,240' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='11110' points='630,270 600,240 660,240' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='11111' points='630,270 600,300 660,300' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='11200' points='630,210 600,240 600,180' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='11201' points='630,210 660,240 660,180' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='11210' points='630,210 600,180 660,180' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='11211' points='630,210 600,240 660,240' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='11300' points='630,150 600,180 600,120' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='11301' points='630,150 660,180 660,120' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='11310' points='630,150 600,120 660,120' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='11311' points='630,150 600,180 660,180' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='11400' points='630,90 600,120 600,60' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='11401' points='630,90 660,120 660,60' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='11410' points='630,90 600,60 660,60' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='11411' points='630,90 600,120 660,120' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='11500' points='630,30 600,60 600,0' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='11501' points='630,30 660,60 660,0' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='11510' points='630,30 600,0 660,0' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='11511' points='630,30 600,60 660,60' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='12100' points='690,270 660,300 660,240' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='12101' points='690,270 720,300 720,240' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='12110' points='690,270 660,240 720,240' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='12111' points='690,270 660,300 720,300' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='12200' points='690,210 660,240 660,180' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='12201' points='690,210 720,240 720,180' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='12210' points='690,210 660,180 720,180' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='12211' points='690,210 660,240 720,240' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='12300' points='690,150 660,180 660,120' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='12301' points='690,150 720,180 720,120' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='12310' points='690,150 660,120 720,120' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='12311' points='690,150 660,180 720,180' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='12400' points='690,90 660,120 660,60' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='12401' points='690,90 720,120 720,60' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='12410' points='690,90 660,60 720,60' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='12411' points='690,90 660,120 720,120' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='12500' points='690,30 660,60 660,0' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='12501' points='690,30 720,60 720,0' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='12510' points='690,30 660,0 720,0' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                    <polygon class='field' id='12511' points='690,30 660,60 720,60' style='fill:#FFFFFF;stroke:#222222;stroke-width:0.3' />
                </svg>
                
                <div class="slide-box play-box">
                    <div class="row">
                        <div class="ten columns">
                            <select id="level">
                                <?php if (!empty($levels)): ?>
                                    <?php foreach ($levels as $level): ?>
                                        <option value="<?php echo $level; ?>">level <?php echo $level; ?></option>
                                    <?php endforeach;?>
                                <?php else: ?>
                                        <option value="0">no levels</option>
                                <?php endif; ?>
                            </select>
                            <button onclick="javascript: Game.play();">play</button>
                        </div>
                        <div class="two columns td-right">
                            <button onclick="javascript: Demo.startDemo();">demo</button>
                        </div>
                    </div>
                    <p>
                        You can select a level and press PLAY button to start a game.<br>
                        <?php if (!$loggedIn): ?>
                            If you want your games to be remembered and your rating to be shown among other players, you need to LOGIN.<br>
                        <?php endif; ?>
                        If you want to learn how the game is played press DEMO button.<br>
                    </p>
                </div>

                <div class="slide-box game-box">
                    <div class="row">
                        <div class="two columns">
                            <button class="select-button" onclick="javascript: Game.fieldsSelected('a');">select</button>
                        </div>

                        <div class="three columns centered">
                            TIMER <span id="timer">60</span> sec
                        </div>

                        <div class="two columns centered">
                            <span id="counter-a">0</span>
                            MOVES
                            <span id="counter-b">0</span>
                        </div>

                        <div class="three columns centered">
                            POINTS:
                            <span id="points" class="points"></span>
                             / 
                            <span class="max-points"></span>
                        </div>

                        <div class="two columns td-right">
                            <button class="quit-button" onclick="javascript: Game.quit();">quit</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="ten columns info"></div>
                        <div class="two columns td-right">
                            <a class="play-demo" href="#" onclick="javascript: Util.showBox(event, 'play');">play/demo</a>
                        </div>
                    </div>
                    <p></p>
                </div>

                <div class="slide-box demo-box">
                    <div class="row button-box">
                        <div class="two columns">
                            <button class="select-button">select</button><span> </span>
                        </div>
                        <div class="eight columns centered">
                            <button class="red" onclick="javascript: Demo.move('a', 0);">&lt;&lt;</button>
                            <span id="demo-counter-a">0</span>
                            <button class="red" onclick="javascript: Demo.move('a', 1);">&gt;&gt;</button>

                            <span class="b-box">
                                <button class="blue" onclick="javascript: Demo.move('b', 1);">&lt;&lt;</button>
                                <span id="demo-counter-b">0</span>
                                <button class="blue" onclick="javascript: Demo.move('b', 0);">&gt;&gt;</button>
                            </span>
                        </div>
                        <div class="two columns"></div>
                    </div>
                    <div class="row">
                        <div class="ten columns info"></div>
                        <div class="two columns td-right">
                            <a href="#" onclick="javascript: Util.showBox(event, 'play');">play/demo</a>
                        </div>
                    </div>
                    <p></p>
                </div>

            </div>

            <?php if ($loggedIn): ?>
                <div class="docs-section" id="history">
                    <h6 class="docs-header">History</h6>
                    <div><?php echo $played; ?></div>
                </div>
            <?php endif; ?>

            <div class="docs-section" id="standings">
                <h6 class="docs-header">Standings</h6>
                <div class="players"><?php echo $standings; ?></div>
            </div>
            
            <div class="docs-section" id="login">
                
                <?php if ($loggedIn): ?>
                    
                    <h6 class="docs-header">Profile</h6>
                    <form method="post" onsubmit="javascript: return Player.validate(this);">
                        <input type="hidden" name="action" value="profile" />
                        <div class="row">
                            <div class="six columns">
                                <input type="text" class="u-full-width" name="username" placeholder="username" autocomplete="off" tabindex="101" 
                                    <?php if ($action == 'profile'): ?> value="<?php echo $username; ?>"<?php else: ?> value="<?php echo $player->username; ?>"<?php endif; ?> />
                            </div>
                            <div class="six columns">
                                <input type="password" class="u-full-width" name="password" placeholder="password" autocomplete="off" tabindex="102" 
                                    <?php if ($action == 'profile'): ?> value="<?php echo $password; ?>"<?php endif; ?> />
                            </div>
                        </div>
                        <div class="row">
                            <div class="six columns">
                                <input type="email" class="u-full-width" name="email" placeholder="email" autocomplete="off" tabindex="104" 
                                    <?php if ($action == 'profile'): ?> value="<?php echo $email; ?>"<?php else: ?> value="<?php echo $player->email; ?>"<?php endif; ?> />
                            </div>
                            <div class="six columns">
                                <input type="password" class="u-full-width" name="repeat_password" placeholder="repeat new password" autocomplete="off" tabindex="103" 
                                    <?php if ($action == 'profile'): ?> value="<?php echo $repeatPassword; ?>"<?php endif; ?> />
                            </div>
                        </div>
                        <input type="submit" class="button" value="edit" tabindex="105" />
                        
                        <?php if ($profile && !empty($message)): ?>
                            <div class="message profile"><?php echo $message; ?></div>
                        <?php else: ?>
                            <div class="message profile hidden"></div>
                        <?php endif; ?>
                        
                    </form>
                    <form method="post">
                        <input type="hidden" name="action" value="logout" />
                        <button id="logout" tabindex="106">logout</button>
                    </form>
                    
                <?php else: ?>
                    
                    <h6 class="docs-header">Login</h6>
                    <form class="login-box" method="post" onsubmit="javascript: return Player.validate(this);">
                        <input type="hidden" name="action" value="login" />
                        <div class="row">
                            <div class="six columns">
                                <input type="text" class="u-full-width" name="username" placeholder="username" tabindex="201" 
                                    <?php if ($action == 'login'): ?> value="<?php echo $username; ?>"<?php endif; ?> />
                            </div>
                            <div class="six columns">
                                <input type="password" class="u-full-width" name="password" placeholder="password" tabindex="202" 
                                    <?php if ($action == 'login'): ?> value="<?php echo $password; ?>"<?php endif; ?>/>
                            </div>
                        </div>
                        <input type="submit" class="button" value="login" tabindex="203" />
                        
                        <?php if ($anchor == 'login' && !empty($message)): ?>
                            <div class="message login"><?php echo $message; ?></div>
                        <?php else: ?>
                            <div class="message login hidden"></div>
                        <?php endif; ?>
                        
                    </form>
                    
                    <?php if (!$register): ?>
                        <a class="anchor" href="#register" onclick="javascript: Util.showSection(event);">register</a>
                    <?php endif; ?>
                    <?php if (!$resetPassword): ?>
                        <a class="anchor u-pull-right" href="#reset-password" onclick="javascript: Util.showSection(event);">forgot your password?</a>
                    <?php endif; ?>
                </div>
                
                <div class="docs-section <?php if (!$register): ?>hidden<?php endif; ?>" id="register">
                    <h6 class="docs-header">Register</h6>
                    <form class="register-box" method="post" onsubmit="javascript: return Player.validate(this);">
                        <input type="hidden" name="action" value="register" />
                        <div class="row">
                            <div class="six columns">
                                <input type="text" class="u-full-width" name="username" placeholder="username" autocomplete="off" tabindex="301" 
                                    <?php if ($action == 'register'): ?> value="<?php echo $username; ?>"<?php endif; ?> />
                            </div>
                            <div class="six columns">
                                <input type="password" class="u-full-width" name="password" placeholder="password" autocomplete="off" tabindex="302" 
                                    <?php if ($action == 'register'): ?> value="<?php echo $password; ?>"<?php endif; ?> />
                            </div>
                        </div>
                        <div class="row">
                            <div class="six columns">
                                <input type="email" class="u-full-width" name="email" placeholder="email" autocomplete="off" tabindex="304" 
                                    <?php if ($action == 'register'): ?> value="<?php echo $email; ?>"<?php endif; ?> />
                            </div>
                            <div class="six columns">
                                <input type="password" class="u-full-width" name="repeat_password" placeholder="repeat password" autocomplete="off" tabindex="303" 
                                    <?php if ($action == 'register'): ?> value="<?php echo $repeatPassword; ?>"<?php endif; ?> />
                            </div>
                        </div>
                        <div class="g-recaptcha" data-sitekey="<?php echo Player::RECAPTCHA_SITE_KEY; ?>"></div>
                        <input type="submit" id="register-submit" class="button" value="register" tabindex="305" />
                        <p>Email is optional and if entered will be used only for retrieving forgotten password.</p>

                        <?php if ($register && !empty($message)): ?>
                            <div class="message register"><?php echo $message; ?></div>
                        <?php else: ?>
                            <div class="message register hidden"></div>
                        <?php endif; ?>

                    </form>
                </div>
                
                <div class="docs-section <?php if (!$resetPassword): ?>hidden<?php endif; ?>" id="reset-password">
                    <h6 class="docs-header">Reset Password</h6>
                    <form class="password-forgotten-box" method="post" onsubmit="javascript: return Player.validate(this);">
                        <input type="hidden" name="action" value="password_forgotten" />
                        <div class="row">
                            <div class="six columns">
                                <input type="text" class="u-full-width" name="username" placeholder="username" autocomplete="off" tabindex="401" 
                                    <?php if ($action == 'password_forgotten'): ?> value="<?php echo $username; ?>"<?php endif; ?> />
                            </div>
                            <div class="six columns">
                                <input type="password" class="u-full-width" name="password" placeholder="new password" autocomplete="off" tabindex="402" 
                                    <?php if ($action == 'password_forgotten'): ?> value="<?php echo $password; ?>"<?php endif; ?> />
                            </div>
                        </div>
                        <div class="row">
                            <div class="six columns">
                                <input type="email" class="u-full-width" name="email" placeholder="email" autocomplete="off" tabindex="404" 
                                    <?php if ($action == 'password_forgotten'): ?> value="<?php echo $email; ?>"<?php endif; ?> />
                            </div>
                            <div class="six columns">
                                <input type="password" class="u-full-width" name="repeat_password" placeholder="repeat new password" autocomplete="off" tabindex="403" 
                                    <?php if ($action == 'password_forgotten'): ?> value="<?php echo $repeatPassword; ?>"<?php endif; ?> />
                            </div>
                        </div>
                        <input type="submit" id="password-forgotten-submit" class="button" value="submit" tabindex="405" />
                        
                        <?php if ($resetPassword && !empty($message)): ?>
                            <div class="message password_forgotten"><?php echo $message; ?></div>
                        <?php else: ?>
                            <div class="message password_forgotten hidden"></div>
                        <?php endif; ?>
                        
                    </form>
                </div>
                
            <?php endif; ?>
            
        </div>
        
        <img src="images/cursor.png" id="pointer" />
        
    </body>
</html>