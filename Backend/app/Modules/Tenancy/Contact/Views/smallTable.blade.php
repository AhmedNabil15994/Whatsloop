<div class="x_content x_content_table">
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>{{ trans('main.group') }}</th>
            <th>{{ trans('main.whatsappNo') }}</th>
            <th>{{ trans('main.name') }}</th>
            <th>{{ trans('main.email') }}</th>
            <th>{{ trans('main.country') }}</th>
            <th>{{ trans('main.city') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $value)
            <tr id="tableRaw{{ $value->id }}">
                {{-- {{ dd($value) }} --}}
                <td>{{ $value->id }}</td>
                <td>{{ $value->group }}</td>
                <td>{{ $value->phone2 }}</td>
                <td>{{ $value->name }}</td>
                <td>{{ $value->email }}</td>
                <td>{{ $value->country }}</td>
                <td>{{ $value->city }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
{{-- @include('Partials.pagination') --}}
<div class="clearfix"></div>