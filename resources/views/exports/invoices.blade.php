<table>
    <thead>
    {{--BASIC INFO--}}
    <tr>
        <th>{{ $invoices[0]->number }}</th>
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
        <th>
            @if($type === 'exports')
                @lang('excel.invoices.export_invoices.title')
            @elseif($type === 'imports')
                @lang('excel.invoices.import_invoices.title')
            @elseif($type === 'refunds')
                @lang('excel.invoices.refund_invoices.title')
            @endif
        </th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th dir="rtl" style="font-size: 20px">
            @lang('excel.invoices.discount')
            @if($invoices[0] -> discount)
                {{ $invoices[0] -> discount }}%
            @else
                0%
            @endif
        </th>
    </tr>

    {{--TAX--}}
    <tr>
        <th>{{ \Carbon\Carbon::parse($invoices[0]->date)->format('d-m-Y') }}</th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th dir="rtl" style="font-size: 20px">
            @lang('excel.invoices.tax')
            @if($invoices[0] -> tax)
                14%
            @else
                0%
            @endif
        </th>
    </tr>

    {{--NET_TOTAL--}}
    <tr>
        <th>
            @if($type === 'exports')
                {{ $invoices[0]->customerBranch->customer->name }}
            @elseif($type === 'imports')
                {{ $invoices[0]->supplier->name }}
            @elseif($type ===  'refunds')
                @if($invoices[0]->type === 'in')
                    {{ $invoices[0]->customerBranch->customer->name }}
                @elseif($invoices[0]->type === 'out')
                    {{ $invoices[0]->supplier->name }}
                @endif
            @endif
        </th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th dir="rtl" style="font-size: 20px">
            @lang('excel.invoices.net_total')
            {{ round($invoices[0]->net_total, 2) }}
            @lang('excel.currency')
        </th>
    </tr>

    {{-- DISCOUNT_AMOUNT --}}
    <tr>
        <th>
            @if($type === 'exports')
                {{ $invoices[0]->customerBranch->address }}
            @elseif($type === 'imports')
                {{ $invoices[0]->supplier->addresses[0]->address }}
            @elseif($type ===  'refunds')
                @if($invoices[0]->type === 'in')
                    {{ $invoices[0]->customerBranch->address }}
                @elseif($invoices[0]->type === 'out')
                    {{ $invoices[0]->supplier->addresses[0]->address }}
                @endif
            @endif
        </th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th dir="rtl" style="font-size: 20px">
            @lang('excel.invoices.discount_amount')
            {{ round($invoices[0]->net_total - ($invoices[0]->net_total * ((100 - $invoices[0]->discount) / 100)), 2) }}
            @lang('excel.currency')
        </th>
    </tr>

    {{-- TAX_AMOUNT --}}
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th dir="rtl" style="font-size: 20px">
            @lang('excel.invoices.tax_amount')
            @if($invoices[0]->tax)
                {{ round(($invoices[0]->net_total * ((100 + 14) / 100)) - $invoices[0]->net_total, 2) }}
            @else
                0
            @endif
            @lang('excel.currency')
        </th>
    </tr>

    {{-- TOTAL --}}
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th dir="rtl" style="font-size: 20px">
            @lang('excel.invoices.total')
            {{ round($invoices[0]->total_after_tax, 2) }}
            @lang('excel.currency')
        </th>
    </tr>
    </thead>

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

