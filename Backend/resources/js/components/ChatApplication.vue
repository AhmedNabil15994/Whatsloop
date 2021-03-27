<template>
    <div>
        <div class="row">
            <div class="col-4">
                <h3>Chat Application</h3>
            </div>
        </div>
    </div>
</template>

<script>
  export default {
    name: 'ChatApplication',
    data: () => {
      return {
        users: [],
        messages: [],
        chatOpen: false,
        chatUserID: null,
        loadingMessages: false,
        newMessage: ''
      }
    },
    created () {
      let app = this
      let fullUrl = window.location.hostname
      let arrs = fullUrl.split('.')
      let domain = arrs[0]
      app.testBroadCastingIncomingMessage(domain)
      app.testBroadCastingBotMessage(domain)
      app.loadDialogs()
    },
    methods: {
      testBroadCastingIncomingMessage (domain) {
        // Start socket.io listener
          Echo.channel(domain+'-NewIncomingMessage')
            .listen('IncomingMessage', (data) => {
              console.log(data)
            })
          // End socket.io listener
      },
      testBroadCastingBotMessage (domain) {
        // Start socket.io listener
          Echo.channel(domain+'-NewBotMessage')
            .listen('BotMessage', (data) => {
              console.log(data)
            })
          // End socket.io listener
      },
      loadDialogs () {
        let app = this
        app.loadingMessages = true
        app.messages = []
        axios.get('livechat/dialogs').then((resp) => {
          console.log(resp.data);
        })
      },
    }
  }
</script>