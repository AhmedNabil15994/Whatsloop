<div class="row data">
    @if($data->type == 'customers')
    @foreach($data->data as $customerKey => $customer)
    <div class="col-sm-12 col-lg-4"> 
        <div class="card custom-card"> 
            <div class="card-body text-center"> 
                <div class="user-lock text-center"> 
                    @php 
                        $colorsArr = ['primary','secondary','info','success','warning','danger','pink'];
                        $fullName = $customer->name;
                        $names = explode(' ',$customer->name,2);
                        $fName = ucfirst($names[0]);
                        $lName = isset($names[1]) ?  ucfirst($names[1]) : ucfirst($names[0]);

                        $abbreviation = mb_substr($fName,0,1,'utf-8') .mb_substr($lName,0,1,'utf-8');;
                    @endphp


                    <div class="avatar avatar-lg d-none d-sm-flex bg-{{ $colorsArr[$customerKey%7] }} rounded-circle">{{ $abbreviation }}</div>
                </div> 
                <h5 class=" mb-1 mt-3 card-title">{{ $customer->name }}</h5> 
                <div class="mt-2 user-info btn-list"> 
                    <a class="btn btn-outline-light btn-block text-right" href="mail:to{{ $customer->email }}">
                        <i class="typcn typcn-mail mr-2 tx-22 lh-1 float-left"></i>
                        <span>{{ $customer->email }}</span>
                    </a> 
                    <a class="btn btn-outline-light btn-block text-right" href="tel:{{ $customer->phone }}">
                        <i class="typcn typcn-phone mr-2 tx-22 lh-1 float-left"></i>
                        <span>{{ $customer->phone }}</span>
                    </a> 
                    <a class="btn btn-outline-light btn-block text-right" href="#">
                        <i class="typcn typcn-map mr-2 tx-22 lh-1 float-left"></i>
                        <span>{{ $customer->country . ($customer->city != '' && $customer->country != '' ? " | "  : '') . $customer->city }}</span>
                    </a> 
                </div> 
            </div> 
        </div> 
    </div>
    @endforeach
    @endif
</div>
<!-- end row -->

@include('tenant.Partials.pagination')