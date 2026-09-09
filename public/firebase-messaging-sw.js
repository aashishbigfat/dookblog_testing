importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');
firebase.initializeApp({
    apiKey: "AIzaSyB3ueelOA5o9HQlWpi_DFS1yZXQoqROpkI",
    authDomain: "blog-34502.firebaseapp.com",
    projectId: "blog-34502",
    storageBucket: "blog-34502.appspot.com",
    messagingSenderId: "514286929789",
    appId: "1:514286929789:web:6c6c709a095f28d737ee14",
    measurementId: "G-5C1RPDJSXG"
});
  
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function({data:{body,title,click_action}}) {
    return self.registration.showNotification(title,{body},{click_action});
});

