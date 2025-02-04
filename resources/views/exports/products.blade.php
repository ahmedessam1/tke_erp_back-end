<table class="table">
    <thead>
    <tr>
        @if(in_array('images', $filtering_options))
            <th>{{ trans('reports.CREDITS.EXCEL_COLUMNS.IMAGE') }}</th>
        @endif
        <th>{{ trans('reports.CREDITS.EXCEL_COLUMNS.NAME') }}</th>
        <th>{{ trans('reports.CREDITS.EXCEL_COLUMNS.CODE') }}</th>
        <th>{{ trans('reports.CREDITS.EXCEL_COLUMNS.BARCODE') }}</th>
        <th>{{ trans('reports.CREDITS.EXCEL_COLUMNS.CATEGORY') }}</th>
        <th>{{ trans('reports.CREDITS.EXCEL_COLUMNS.TOTAL_QUANTITY') }}</th>
        @if (Auth::user()->hasRole(['super_admin']))
            <th>{{ trans('reports.CREDITS.EXCEL_COLUMNS.AVG_PURCHASE_PRICE') }}</th>
            <th>{{ trans('reports.CREDITS.EXCEL_COLUMNS.TOTAL_CREDIT') }}</th>
        @endif
    </tr>
    </thead>
    <tbody>
        @foreach($products as $key => $product)
            <tr>
                @if(in_array('images', $filtering_options))
                    <td>
                        @if(is_file(public_path('storage/uploads/'.Auth::user()->tenant->domain.'/products/main/'.$product->images[0]->large_image)))
                            <img height="80px" width="80px" src="{{ public_path('storage/uploads/'.Auth::user()->tenant->domain.'/products/main/'.$product->images[0]->large_image) }}" alt="">
                        @else
                            <img height="80px" width="80px" src="{{ public_path() }}/assets/placeholder.png" alt="">
                        @endif
                    </td>
                @endif
                <td>{{ $product->name }}</td>
                <td>{{ $product->code }}</td>
                <td><span>{{ $product->barcode }}</span></td>
                <td>{{ $product->category->name }}</td>
                <td>{{ $product->report_total_quantity }}</td>
                <td>{{ $product->report_avg_purchase_price }}</td>
                <td>{{ $product->report_total_credit }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
