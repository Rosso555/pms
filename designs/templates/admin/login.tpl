<!DOCTYPE html>
<html lang="en">
<head>
  <title>PMS-LOGIN</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="/css/css_login.css" media="screen" title="no title">
</head>
<body>
  <div class="container" style="margin-top:70px;">
  	<div id="login-box" class="logo">
      {if $error }
      <div class="alert alert-warning" data-dismiss="alert">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {if $error.username eq 1} <i class="fa fa-exclamation"></i>!&nbsp;Please enter your username. <br />{/if}
        {if $error.password eq 1} <i class="fa fa-exclamation"></i>!&nbsp;Please enter your password. <br />{/if}
        {if $error.login eq 1}    <i class="fa fa-exclamation"></i>!&nbsp;Wrong username or password. <br />{/if}
      </div>
      {/if}
  		<div class="logo">
  			<img src="/images/con_loin.jpg" class="img img-responsive img-circle center-block"/>
  			<h1 class="logo-caption"><span class="tweak">L</span>ogin</h1>
  		</div><!-- /.logo -->
  		<div class="controls">
        <form class="" action="{$admin_file}?task=login" method="post">
          <input type="text" name="username" placeholder="Username" class="form-control" />
          <input type="password" name="password" placeholder="Password" class="form-control" />
          <button type="submit" class="btn btn-default btn-block btn-custom">Login</button>
        </form>
  		</div><!-- /.controls -->
  	</div><!-- /#login-box -->
  </div><!-- /.container -->
  <div id="particles-js"></div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript">
$.getScript("https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js", function(){
  particlesJS('particles-js',
    {
      "particles": {
        "number": {
          "value": 80,
          "density": {
            "enable": true,
            "value_area": 800
          }
        },
        "color": {
          "value": "#ffffff"
        },
        "shape": {
          "type": "circle",
          "stroke": {
            "width": 0,
            "color": "#000000"
          },
          "polygon": {
            "nb_sides": 5
          },
          "image": {
            "width": 100,
            "height": 100
          }
        },
        "opacity": {
          "value": 0.5,
          "random": false,
          "anim": {
            "enable": false,
            "speed": 1,
            "opacity_min": 0.1,
            "sync": false
          }
        },
        "size": {
          "value": 5,
          "random": true,
          "anim": {
            "enable": false,
            "speed": 40,
            "size_min": 0.1,
            "sync": false
          }
        },
        "line_linked": {
          "enable": true,
          "distance": 150,
          "color": "#ffffff",
          "opacity": 0.4,
          "width": 1
        },
        "move": {
          "enable": true,
          "speed": 6,
          "direction": "none",
          "random": false,
          "straight": false,
          "out_mode": "out",
          "attract": {
            "enable": false,
            "rotateX": 600,
            "rotateY": 1200
          }
        }
      },
      "interactivity": {
        "detect_on": "canvas",
        "events": {
          "onhover": {
            "enable": true,
            "mode": "repulse"
          },
          "onclick": {
            "enable": true,
            "mode": "push"
          },
          "resize": true
        },
        "modes": {
          "grab": {
            "distance": 400,
            "line_linked": {
              "opacity": 1
            }
          },
          "bubble": {
            "distance": 400,
            "size": 40,
            "duration": 2,
            "opacity": 8,
            "speed": 3
          },
          "repulse": {
            "distance": 200
          },
          "push": {
            "particles_nb": 4
          },
          "remove": {
            "particles_nb": 2
          }
        }
      },
      "retina_detect": true,
      "config_demo": {
        "hide_card": false,
        "background_color": "#b61924",
        "background_image": "",
        "background_position": "50% 50%",
        "background_repeat": "no-repeat",
        "background_size": "cover"
      }
    }
  );

});

</script>
</body>
</html>
