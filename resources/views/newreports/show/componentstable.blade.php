{{-- resources/views/components/table.blade.php --}}
<table id="{{ $id }}" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>
                <span class="checkbox-toggle" id="{{$checklist}}"><i class="far fa-square"></i></span>
            </th>
            <th scope="col">No</th>
            <th scope="col">{{ $documentNoHeader }}</th>
            <th scope="col">{{ $documentNameHeader }}</th>
            <th scope="col">Jenis Dokumen</th>
            <th scope="col">Rev Terakhir</th>
            <th scope="col">Realisasi</th>

            <th scope="col">Paper Size</th>
            <th scope="col">Sheet</th>


            <th scope="col">Status</th>
            <th scope="col">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @php
            $penghitung = 1;
        @endphp

        @foreach ($progressReports as $index => $progressReport)
                <tr>
                    <td>
                        <div class="icheck-primary">
                            <input type="checkbox" value="{{ $progressReport->id }}" name="{{$name}}"
                                id="checkbox{{ $progressReport->id }}" onchange="handleCheckboxChange(this)">
                            <label for="checkbox{{ $progressReport->id }}"></label>
                        </div>
                    </td>
                    <td>{{ $penghitung }}</td>



                    @if (auth()->user()->rule == "MTPR")
                        <td>
                            <span id="documentNumberDisplay{{ $progressReport->id }}">
                                {{ $progressReport->nodokumen ?? 'No Document' }}
                            </span>
                            <button class="btn btn-sm btn-outline-primary" onclick="enableEdit({{ $progressReport->id }})">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <form id="editDocumentForm{{ $progressReport->id }}" style="display: none;">
                                <input type="text" name="nodokumen" id="nodokumen{{ $progressReport->id }}"
                                    value="{{ $progressReport->nodokumen ?? '' }}" class="form-control d-inline-block"
                                    style="width: auto;" placeholder="Masukkan nomor dokumen">
                                <button type="button" class="btn btn-sm btn-success" id="saveButton{{ $progressReport->id }}"
                                    onclick="updateDocumentNumber({{ $progressReport->id }}, {{ $progressReport->newreport_id }}, '{{ $progressReport->nodokumen ?? '' }}')">
                                    Save
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary"
                                    onclick="cancelEdit({{ $progressReport->id }})">Cancel</button>
                            </form>
                        </td>

                    @else
                        <td>{{ $progressReport->nodokumen }}</td>
                    @endif







                    <td id="namadokumen_{{ $progressReport->id }}_{{ $index }}">
                        {{ $progressReport->namadokumen ?? "" }}
                    </td>


                    @if (auth()->user()->rule == "MTPR")
                        <td>
                            <select class="form-control select-documentkind" data-id="{{ $progressReport->id }}"
                                data-index="{{ $index }}">
                                <option value="">-- Pilih Jenis Dokumen --</option>
                                @foreach($jenisdokumen as $jenis)
                                    <option value="{{ $jenis->id }}" {{ $progressReport->documentkind_id == $jenis->id ? 'selected' : '' }}>
                                        {{ $jenis->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    @else
                        <td id="documentkind_{{ $progressReport->id }}_{{ $index }}">
                            {{ $progressReport->documentKind->name ?? '' }}
                        </td>
                    @endif








                    <td id="rev_{{ $progressReport->id }}_{{ $index }}">{{ $progressReport->rev ?? "" }}</td>
                    <td id="realisasi_{{ $progressReport->id }}_{{ $index }}">
                        {{ \Carbon\Carbon::parse($progressReport->realisasidate)->format('d/m/Y') }}
                    </td>

                    <td id="papersize_{{ $progressReport->id }}_{{ $index }}">
                        {{ $progressReport->papersize }}
                    </td>
                    <td id="sheet_{{ $progressReport->id }}_{{ $index }}">
                        {{ $progressReport->sheet }}
                    </td>
                    <td id="status_{{ $progressReport->id }}_{{ $index }}">
                        {{ $progressReport->status }}
                    </td>
                    @php
                        $hasilwaktu = json_decode($progressReport->temporystatus, true);
                    @endphp
                    <td>
                        @if($newreport_id)
                            <a href="{{ route('newreports.showrev', ['newreport' => $newreport_id, 'id' => $progressReport->id]) }}"
                                class="btn btn-success btn-sm d-block mb-1">
                                <i class="fas fa-history"></i> Revisi
                            </a>
                            @if (auth()->user()->name == "Dian Pertiwi" || $progressReport->status != "RELEASED")
                                <a href="#" class="btn btn-default bg-kakhi d-block mb-1"
                                    onclick="opendeleteForm('{{ $progressReport->id }}', '{{ $index }}')">
                                    <i class="fas fa-eraser"></i> Delete
                                </a>
                            @endif
                        @endif
                        <!-- @if($useronly->rule == "Manager ".$newreport->unit || $useronly->rule == "MTPR" || $useronly->rule == "superuser" || $useronly->rule == "Senior Manager Engineering")
                                                                                                                                                                                                                                                                                                                                <a href="#" class="btn btn-info btn-sm d-block mb-1" onclick="showDocumentSummary('{{ json_encode($progressReport) }}', '{{ $progressReport->id }}', '{{ $index }}', '{{ json_encode($listanggota) }}', '{{ $useronly->rule }}')">
                                                                                                                                                                                                                                                                                                                                    <i class="fas fa-edit"></i> Edit
                                                                                                                                                                                                                                                                                                                                </a>

                                                                                                                                                                                                                                                                                                                                <a href="#" class="btn btn-default bg-kakhi d-block mb-1" onclick="opendeleteForm('{{ $progressReport->id }}', '{{ $index }}')">
                                                                                                                                                                                                                                                                                                                                    <i class="fas fa-eraser"></i> Delete
                                                                                                                                                                                                                                                                                                                                </a>
                                                                                                                                                                                                                                                                                                                            @endif -->

                        <!-- <a href="#" class="btn btn-default bg-maroon d-block mb-1" id="buttondetailtugas_{{ $progressReport->id }}_{{ $index }}" onclick="detailtugas('{{ $progressReport->id }}')">
                                                                                                                                                                                                                                                                                                                                <i class="fas fa-info-circle"></i> Detail
                                                                                                                                                                                                                                                                                                                            </a> -->
                    </td>

                </tr>
                @php
                    $penghitung++;
                @endphp
        @endforeach
    </tbody>
</table>