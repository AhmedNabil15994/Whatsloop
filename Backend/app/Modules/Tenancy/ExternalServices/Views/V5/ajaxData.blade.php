<div class="row users">
    @foreach($data->data as $customerKey => $customer)
    <div class="col-md-4">
        <div class="customer">
            <i class="icon">
                <svg id="_006-user-1" data-name="006-user-1" xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36">
                  <g id="Group_1659" data-name="Group 1659">
                    <g id="Group_1658" data-name="Group 1658">
                      <path id="Path_1197" data-name="Path 1197" d="M18,0A18,18,0,1,0,36,18,18.02,18.02,0,0,0,18,0Zm0,33.15A15.15,15.15,0,1,1,33.15,18,15.168,15.168,0,0,1,18,33.15Z" fill="#fff"></path>
                    </g>
                  </g>
                  <g id="Group_1661" data-name="Group 1661" transform="translate(4.664 21.194)">
                    <g id="Group_1660" data-name="Group 1660">
                      <path id="Path_1198" data-name="Path 1198" d="M79.666,301.425c-5.062,0-9.8,2.539-13.336,7.147l2.261,1.734c2.986-3.89,6.919-6.032,11.075-6.032s8.09,2.142,11.075,6.032L93,308.572C89.465,303.964,84.728,301.425,79.666,301.425Z" transform="translate(-66.33 -301.425)" fill="#fff"></path>
                    </g>
                  </g>
                  <g id="Group_1663" data-name="Group 1663" transform="translate(10.591 6.459)">
                    <g id="Group_1662" data-name="Group 1662">
                      <path id="Path_1199" data-name="Path 1199" d="M158.037,91.863a7.457,7.457,0,1,0,7.409,7.457A7.441,7.441,0,0,0,158.037,91.863Zm0,12.063a4.607,4.607,0,1,1,4.559-4.607A4.588,4.588,0,0,1,158.037,103.926Z" transform="translate(-150.628 -91.863)" fill="#fff"></path>
                    </g>
                  </g>
                </svg>
            </i>
            <h2 class="name">{{ $customer->name }}</h2>
            <a href="mail:to{{ $customer->email }}" class="numb">{{ $customer->email }} <i class="flaticon-email"></i></a>
            <a href="tel:{{ $customer->phone }}" class="numb">{{ $customer->phone }} <i class="flaticon-phone-call"></i></a>
            <a href="#" class="numb">{{ $customer->country . ($customer->city != '' && $customer->country != '' ? " | "  : '') . $customer->city }} <i class="flaticon-map"></i></a>
        </div>
    </div>
    @endforeach
</div>

@include('tenant.Partials.pagination')