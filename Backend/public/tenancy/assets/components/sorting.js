!function(n){
    "use strict";
    function t(){
        this.$body=n("body")
    }
    t.prototype.init=function(){
        n(".tasklist").each(function(){
            Sortable.create(
                n(this)[0],{
                    group:"shared",
                    animation:150,
                    ghostClass:"bg-ghost",
                    onEnd: function() {
                        var ids = [];
                        var sorts = [];
                        $(".tasklist li").each(function (index, element) {
                            ids.push($(element).attr("data-id"));
                            sorts.push(index + 1);
                        });

                        var url = window.location.href;
                        if(url.indexOf("#") != -1){
                            url = url.replace('#','');
                        }

                        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
                        $.ajax({
                            type:'post',
                            url: url+'/sort',
                            data:{
                                '_token': $('meta[name="csrf-token"]').attr('content'),
                                'ids': JSON.stringify(ids),
                                'sorts': JSON.stringify(sorts),
                            },
                            success:function(data){
                                if (data.status.status == 1) {
                                    successNotification(data.status.message);
                                } else {
                                    errorNotification(data.status.message);
                                }
                            },
                        }); 
                    },
                })
        })
    };
    n.KanbanBoard=new t;
    n.KanbanBoard.Constructor=t
}
(window.jQuery),function(){
    "use strict";
    window.jQuery.KanbanBoard.init()
}();