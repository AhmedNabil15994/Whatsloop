curl \
-d '{"CHANNELID": "xxxxx","CHANNELTOKEN": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx","phone": "966xxxxxxxxx","title":"hello","body": "اهلا بك فى واتس لوب تجربة ارسال ازرار تفاعلية", "footer":"bye bye" , "buttons": "Option 1,Option 2,Option 3"}' \ 
-X POST \ 
"https://wloop.net/engine/messages/sendButtons"