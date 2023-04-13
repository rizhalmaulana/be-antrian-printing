<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h2>Hello</h2>


    <script src="https://www.gstatic.com/firebasejs/8.2.2/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.2.2/firebase-messaging.js"></script>
    
    <script>
        var firebaseConfig = {
            apiKey: "AIzaSyBod8eX-AsUv5pZc3Ka9WpedSrvnLYM4e4",
            authDomain: "antrian-printing.firebaseapp.com",
            projectId: "antrian-printing",
            storageBucket: "antrian-printing.appspot.com",
            messagingSenderId: "627524827025",
            appId: "1:627524827025:web:49800c5eeffb48cbe50c30",
            measurementId: "G-BX9PDP7K87"
        };

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        
        const fcm = firebase.messaging();

        fcm.getToken({
            vapidKey: "BJbCaBg7i5BefI3lO-yQg_5lbKAOYVygztgCoIum8TElTso1edVknWFCU50TKN7mVFuiu_DcaI0f3xvewqnx2GU"
        }).then((currentToken) => {
            console.log('', currentToken);
        });
    </script>

</body>

</html>