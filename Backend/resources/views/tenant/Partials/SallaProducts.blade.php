<div class="modal fade" id="modal-salla-products" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans('main.assignSallaProduct') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form">
                    <label class="col-3 col-form-label">{{ trans('main.products') }} :</label>
                    <div class="col-9">
                        <select name="product_id" data-toggle="select2" class="form-control">
                            <option value="0">{{ trans('main.choose') }}</option>
                            @foreach($data->salla_products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success assignProduct">{{ trans('main.save') }}</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{ trans('main.back') }}</button>
            </div>
        </div>
    </div>
</div>