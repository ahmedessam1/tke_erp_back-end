<table>
    <thead>
    {{--BASIC INFO--}}
    <tr>
        <th dir="rtl" style="font-size: 20px">
            @if($invoices[0] -> approve)
                <b>@lang('excel.invoices.approved')</b>
            @else
                <b>@lang('excel.invoices.not_approved')</b>
            @endif
        </th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th dir="rtl" style="font-size: 20px">
            @if($type === 'exports')
                @lang('excel.invoices.export_invoices.title'):
            @elseif($type === 'imports')
                @lang('excel.invoices.import_invoices.title'):
            @elseif($type === 'refunds')
                @lang('excel.invoices.refund_invoices.title'):
            @endif

            @if($type === 'refunds')
                {{ $invoices[0] -> refund_title }}
            @else
                {{ $invoices[0] -> title }}
            @endif
        </th>
    </tr>
    {{--DISCOUNT--}}
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th style="font-size: 16px">
            @lang('excel.invoices.discount'):
            @if($invoices[0] -> discount)
                {{ $invoices[0] -> discount }}%
            @else
                0%
            @endif
        </th>
    </tr>
    {{--TAX--}}
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th style="font-size: 16px">
            @lang('excel.invoices.tax'):
            @if($invoices[0] -> tax)
                14%
            @else
                0%
            @endif
        </th>
    </tr>
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th dir="rtl" style="font-size: 20px">
            @lang('excel.invoices.total_before_discount')
            {{ round($invoices[0] -> net_total) }}
            @lang('excel.currency')
        </th>
    </tr>
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th dir="rtl" style="font-size: 20px">
            @lang('excel.invoices.total_after_discount')
            {{ round($invoices[0] -> total_after_discount) }}
            @lang('excel.currency')
        </th>
    </tr>
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th dir="rtl" style="font-size: 20px">
            @lang('excel.invoices.total_after_tax')
            {{ round($invoices[0] -> total_after_tax) }}
            @lang('excel.currency')
        </th>
    </tr>
    </thead>
</table>


<table class="table">
    <thead>
    <tr>
        @if($type === 'exports')
            <th style="font-size: 16px; text-align: center">
                <b>@lang('excel.invoices.export_invoices.products_table.sold_price')</b></th>
            <th style="font-size: 16px; text-align: center">
                <b>@lang('excel.invoices.export_invoices.products_table.discount')</b></th>
            <th style="font-size: 16px; text-align: center">
                <b>@lang('excel.invoices.export_invoices.products_table.quantity')</b></th>
            <th style="font-size: 16px; text-align: center">
                <b>@lang('excel.invoices.export_invoices.products_table.barcode')</b></th>
            <th style="font-size: 16px; text-align: center">
                <b>@lang('excel.invoices.export_invoices.products_table.code')</b></th>
            <th style="font-size: 16px; text-align: center">
                <b>@lang('excel.invoices.export_invoices.products_table.name')</b></th>
        @elseif($type === 'imports')
            <th style="font-size: 16px; text-align: center">
                <b>@lang('excel.invoices.import_invoices.products_table.purchase_price')</b></th>
            <th style="font-size: 16px; text-align: center">
                <b>@lang('excel.invoices.import_invoices.products_table.discount')</b></th>
            <th style="font-size: 16px; text-align: center">
                <b>@lang('excel.invoices.import_invoices.products_table.quantity')</b></th>
            <th style="font-size: 16px; text-align: center">
                <b>@lang('excel.invoices.import_invoices.products_table.barcode')</b></th>
            <th style="font-size: 16px; text-align: center">
                <b>@lang('excel.invoices.import_invoices.products_table.code')</b></th>
            <th style="font-size: 16px; text-align: center">
                <b>@lang('excel.invoices.import_invoices.products_table.name')</b></th>
        @elseif($type === 'refunds')
            <th style="font-size: 16px; text-align: center">
                <b>@lang('excel.invoices.refund_invoices.products_table.price')</b></th>
            <th style="font-size: 16px; text-align: center">
                <b>@lang('excel.invoices.refund_invoices.products_table.discount')</b></th>
            <th style="font-size: 16px; text-align: center">
                <b>@lang('excel.invoices.refund_invoices.products_table.quantity')</b></th>
            <th style="font-size: 16px; text-align: center">
                <b>@lang('excel.invoices.refund_invoices.products_table.barcode')</b></th>
            <th style="font-size: 16px; text-align: center">
                <b>@lang('excel.invoices.refund_invoices.products_table.code')</b></th>
            <th style="font-size: 16px; text-align: center">
                <b>@lang('excel.invoices.refund_invoices.products_table.name')</b></th>
        @endif
    </tr>
    </thead>

    <tbody>
    @foreach($invoices as $invoice)
        @if($type === 'exports')
            @foreach($invoice -> soldProducts as $sold_product)
                <tr>
                    <td style="text-align: center">{{ $sold_product -> sold_price }}</td>
                    <td style="text-align: center">{{ $sold_product -> discount }}</td>
                    <td style="text-align: center">{{ $sold_product -> quantity }}</td>
                    <td style="text-align: center">{{ $sold_product -> product -> barcode }}</td>
                    <td style="text-align: center">{{ $sold_product -> product -> code }}</td>
                    <td style="text-align: center">{{ $sold_product -> product -> name }}</td>
                </tr>
            @endforeach
        @elseif($type === 'imports')
            @foreach($invoice -> productCredits as $purchase_product)
                <tr>
                    <td style="text-align: center">{{ $purchase_product -> purchase_price }}</td>
                    <td style="text-align: center">{{ $purchase_product -> discount }}</td>
                    <td style="text-align: center">{{ $purchase_product -> quantity }}</td>
                    <td style="text-align: center">{{ $purchase_product -> product -> barcode }}</td>
                    <td style="text-align: center">{{ $purchase_product -> product -> code }}</td>
                    <td style="text-align: center">{{ $purchase_product -> product -> name }}</td>
                </tr>
            @endforeach
        @elseif($type === 'refunds')
            @foreach($invoice -> refundedProducts as $refunded_product)
                <tr>
                    <td style="text-align: center">{{ $refunded_product -> price }}</td>
                    <td style="text-align: center">{{ $refunded_product -> discount }}</td>
                    <td style="text-align: center">{{ $refunded_product -> quantity }}</td>
                    <td style="text-align: center">{{ $refunded_product -> product -> barcode }}</td>
                    <td style="text-align: center">{{ $refunded_product -> product -> code }}</td>
                    <td style="text-align: center">{{ $refunded_product -> product -> name }}</td>
                </tr>
            @endforeach
        @endif
    @endforeach
    </tbody>
</table>

