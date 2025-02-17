<table class="table table-bordered">
    <tbody>
        @foreach ($progressReports as $progressReport)
            <tr>
                <td>
                    @if (isset($generasi[$progressReport->id]) && !empty($generasi[$progressReport->id]['count']))
                        <button class="btn btn-primary toggle-children" data-id="{{ $progressReport->id }}">+</button>
                    @else
                        <button class="btn btn-secondary" disabled>[]</button>
                    @endif
                </td>
                <td>{{ $progressReport->nodokumen }}</td>
                <td>{{ $progressReport->namadokumen }}</td>
                <td>{{ $progressReport->status }}</td>
            </tr>
            @if (isset($generasi[$progressReport->id]) && !empty($generasi[$progressReport->id]['count']))
                <tr class="child-rows" data-parent-id="{{ $progressReport->id }}" style="display: none;">
                    <td colspan="5">
                        @include('newreports.child', ['progressReports' => $generasi[$progressReport->id]['childreen'], 'generasi' => $generasi])
                    </td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
