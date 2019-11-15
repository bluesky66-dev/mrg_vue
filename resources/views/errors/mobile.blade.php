<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Device Not Supported!</title>
  <style>
  #overlay-wrapper{
  position: fixed;
  overflow: hidden;
}

#overlay {
  position: fixed;
  padding: 0;
  margin: 0;
  top: 0;
  left: 0;
  z-index: 9999;
  height: 100vh;
  width: 100vw;
  background: #027de4;
  -webkit-box-shadow: inset 0px 1px 60px -8px rgba(0, 0, 0, 0.75);
  -moz-box-shadow: inset 0px 1px 60px -8px rgba(0, 0, 0, 0.75);
  box-shadow: inset 0px 1px 60px -8px rgba(0, 0, 0, 0.75);

}
#overlay div {
  position: relative;
  top: 20%;
  left: 15%;
  width: 70%;
  height: 50%;
  background: white;
  -webkit-box-shadow: 0px 1px 20px 3px rgba(0, 0, 0, 0.75);
  -moz-box-shadow: 0px 1px 20px 3px rgba(0, 0, 0, 0.75);
  box-shadow: 0px 1px 20px 3px rgba(0, 0, 0, 0.75);
}
#overlay div h3,
#overlay div p {
  padding: 1em;
  text-align: center;
}
</style>

</head>
<body>
<div id="overlay-wrapper">
<div id="overlay">
    <div>
        <h3>Device Not Supported!</h3>
        <hr/>
        <p>We're sorry! Your mobile device is not supported, yet! Please access Momentum from a desktop computer for the complete experience!</p>
    </div>
  </div>
</div>
</body>
</html>
