<div class="modal fade" id="AdvancedSearchHelp" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans('main.advancedSearchTip') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="lead">
                    {{ trans('main.tip1') }}
                    <br>
                    {{ trans('main.tip2') }}
                    <br>
                    {{ trans('main.tip3') }}
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">{{ trans('main.thanks') }}</button>
            </div>
        </div>
    </div>
</div>