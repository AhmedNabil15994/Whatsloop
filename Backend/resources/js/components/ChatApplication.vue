<template>
    <div>
        <div class="row">
            <div class="col-4">
                <h3>Users list</h3>
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
      app.testBroadCasting()
    },
    methods: {
      testBroadCasting () {
        // Start socket.io listener
          Echo.channel('newMessage-201069273925')
            .listen('SendMessage', (data) => {
              console.log(data)
            })
          // End socket.io listener
      },
    }
  }
</script>