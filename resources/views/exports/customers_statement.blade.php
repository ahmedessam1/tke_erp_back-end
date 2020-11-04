@foreach($data as $key => $collection)
    <table class="table">
        <thead>
            <tr>
                <th dir="rtl" style="font-size: 22px">
                    @lang('excel.customers_statement.title'):
                    {{ $key }}
                </th>
            </tr>

            <tr>
                <th style="font-size: 18px; text-align: center; font-weight: bold; background-color: #1f497d; color: #ffffff">@lang('excel.customers_statement.table.credit')</th>
                <th style="font-size: 18px; text-align: center; font-weight: bold; background-color: #1f497d; color: #ffffff">@lang('excel.customers_statement.table.debtor')</th>
                <th style="font-size: 18px; text-align: center; font-weight: bold; background-color: #1f497d; color: #ffffff">@lang('excel.customers_statement.table.creditor')</th>
                <th style="font-size: 18px; text-align: center; font-weight: bold; background-color: #1f497d; color: #ffffff">@lang('excel.customers_statement.table.statement')</th>
                <th style="font-size: 18px; text-align: center; font-weight: bold; background-color: #1f497d; color: #ffffff">@lang('excel.customers_statement.table.date')</th>
                <th style="font-size: 18px; text-align: center; font-weight: bold; background-color: #1f497d; color: #ffffff">@lang('excel.customers_statement.table.branch_name')</th>
            </tr>
        </thead>

        <tbody>
        @php $total = 0; @endphp
        @foreach($collection as $c)
            @php
                $color = '#000000';
                $sign = '';
                $append_to_name = '';
                if ($c['type'] === 'export_invoice')
                    $total += $c['total'];
                else if($c['type'] === 'payment') {
                    $color = '#FF0000';
                    $total -= $c['total'];
                    $sign = '-';
                    $append_to_name = '(' . trans('excel.customers_statement.payment') . ')';
                } else if($c['type'] === 'initiatory_credit') {
                    $color = '#4175b8';
                    $total += $c['total'];
                    $append_to_name = '(' . trans('excel.customers_statement.initiatory_credit') . ')';
                } else {
                    $total -= $c['total'];
                    $color = '#FF0000';
                    $sign = '-';
                    $append_to_name = '(' . trans('excel.customers_statement.refund') . ')';
                }
            @endphp
            <tr>
                <td style="text-align: center;  color: {{ $color }}">{{ $total }}</td>
                @if($sign === '-')
                    <td style="text-align: center; color: {{ $color }}"></td>
                    <td style="text-align: center; color: {{ $color }}">{{ $c['total'] }}</td>
                @else
                    <td style="text-align: center; color: {{ $color }}">{{ $c['total'] }}</td>
                    <td style="text-align: center; color: {{ $color }}"></td>
                @endif
                <td style="text-align: center; color: {{ $color }}">{{ $c['invoice_number'] }}</td>
                <td style="text-align: center; color: {{ $color }}">{{ $c['date'] }}</td>
                <td style="text-align: center; color: {{ $color }}">{{ $c['branch_name'] }} {{ $append_to_name }}</td>
            </tr>
        @endforeach
        </tbody>

        <tfoot>
            <tr dir="rtl">
                <td style="font-size: 16px; font-weight: bold; background-color: #1f497d; color: #ffffff">{{ $total }}</td>
                <td style="font-size: 16px; font-weight: bold; background-color: #1f497d; color: #ffffff">@lang('excel.customers_statement.table.total')</td>
            </tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
        </tfoot>
    </table>
@endforeach

