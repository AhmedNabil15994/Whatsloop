<div class="modal fade" id="transferDaysModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans('main.add_days') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-3 col-form-label">{{ trans('main.days') }} :</label>
                    <div class="col-9">
                        <input class="form-control" name="days" placeholder="{{ trans('main.days') }}">
                    </div>
                </div> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success">{{ trans('main.save') }}</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{ trans('main.back') }}</button>
            </div>
        </div>
    </div>
</div>