<div class="modal fade" id="unknownBot" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans('main.unknownReply') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form">
                    <label class="col-3 col-form-label">{{ trans('main.messageContent') }} :</label>
                    <div class="col-9">
                    	<textarea name="message" class="form-control" placeholder="{{ trans('main.messageContent') }}">{{ \App\Models\Variable::getVar('UNKNOWN_BOT_REPLY') }}</textarea>
                    </div>
                </div> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success addBotReply">{{ trans('main.save') }}</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{ trans('main.back') }}</button>
            </div>
        </div>
    </div>
</div>