@extends('layouts.main')

@section('container1')
<div class="container">
    
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">Buat Hazard Log</h3>
        </div>

        <div class="card-body">
            <form action="{{ url('hazard_logs') }}" method="POST">
                @csrf

                <div class="row">

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="hazard_ref">Hazard Ref</label>
                            <input type="text" class="form-control" id="hazard_ref" name="hazard_ref" required>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="operating_mode">Operating Mode</label>
                            <input type="text" class="form-control" id="operating_mode" name="operating_mode">
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="system">System</label>
                            <input type="text" class="form-control" id="system" name="system">
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="hazard">Hazard</label>
                            <input type="text" class="form-control" id="hazard" name="hazard">
                        </div>
                    </div>
                    

                </div>

                <div class="row">

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="hazard_cause">Hazard Cause</label>
                            <input type="text" class="form-control" id="hazard_cause" name="hazard_cause">
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="accident">Accident</label>
                            <input type="text" class="form-control" id="accident" name="accident">
                        </div>
                    </div>
                    

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="verification_evidence_reference">Verification Evidence Reference</label>
                            <input type="text" class="form-control" id="verification_evidence_reference" name="verification_evidence_reference">
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="validation_evidence_reference">Validation Evidence Reference</label>
                            <input type="text" class="form-control" id="validation_evidence_reference" name="validation_evidence_reference">
                        </div>
                    </div>
                    

                </div>

                <div class="row">
                    <div class="form-group col-sm-3">
                        <label for="IF">IF</label>
                        <input type="text" class="form-control" id="IF" name="IF">
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="IS">IS</label>
                        <input type="text" class="form-control" id="IS" name="IS">
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="IR">IR</label>
                        <input type="text" class="form-control" id="IR" name="IR">
                        <button type="button" class="btn btn-secondary mt-2" onclick="generateIR()">IR Generate</button>
                    </div>
                </div>
                
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label for="resolution_status">Resolution Status</label>
                        <select class="form-control" id="resolution_status" name="resolution_status">
                            <option value="Open">Open</option>
                            <option value="Closed">Closed</option>
                            <option value="Deleted">Deleted</option>
                            <option value="Verified">Verified</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="hazard_status">Hazard Status</label>
                        <select class="form-control" id="hazard_status" name="hazard_status">
                            <option value="Open">Open</option>
                            <option value="Closed">Closed</option>
                            <option value="Deleted">Deleted</option>
                            <option value="Open with Design Verification Completed">Open with Design Verification Completed</option>
                        </select>
                    </div>

                </div>

                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="source">Source</label>
                            <input type="text" class="form-control" id="source" name="source">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="haz_owner">Haz Owner</label>
                            <input type="text" class="form-control" id="haz_owner" name="haz_owner">
                        </div>
                    </div>
                    
                </div>
                
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="RF">RF</label>
                        <input type="text" class="form-control" id="RF" name="RF">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="RS">RS</label>
                        <input type="text" class="form-control" id="RS" name="RS">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="RR">RR</label>
                        <input type="text" class="form-control" id="RR" name="RR">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="comments">Comments</label>
                    <textarea class="form-control" id="comments" name="comments"></textarea>
                </div>

                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="due_date">Due Date</label>
                            <input type="date" class="form-control" id="due_date" name="due_date">
                        </div>
                    </div>
                </div>

                
                <div class="form-group">
                    <label for="proyek_type">Project:</label>
                    <select class="form-control" name="proyek_type" id="proyek_type">
                        @foreach($listproject as $project)
                            <option value="{{ $project }}">{{ $project }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Project PIC:</label><br>
                    @foreach($listpic as $pic)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="hazard_unit[]" value="{{ $pic }}" id="pic_{{ $loop->index }}" onchange="toggleReductionMeasure({{ $loop->index }})">
                            <label class="form-check-label">{{ $pic }}</label>
                        </div>
                        <div class="form-group" id="reduction_measure_{{ $loop->index }}_container" style="display:none;">
                            <label for="reduction_measure_{{ $loop->index }}">Risk Reduction Measure for {{ $pic }}</label>
                            <input type="text" class="form-control" id="reduction_measure_{{ $loop->index }}" name="reduction_measures[]">
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleReductionMeasure(index) {
        var checkbox = document.getElementById('pic_' + index);
        var container = document.getElementById('reduction_measure_' + index + '_container');
        if (checkbox.checked) {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    }

    function generateIR() {
        var IF = document.getElementById('IF').value.toUpperCase();
        var IS = document.getElementById('IS').value.toUpperCase();
        var IR = document.getElementById('IR');

        var table = {
            'A': ['UND', 'INT', 'INT', 'INT'],
            'B': ['TOL', 'TOL', 'INT', 'INT'],
            'C': ['NEG', 'UND', 'UND', 'INT'],
            'D': ['NEG', 'NEG', 'TOL', 'UND'],
            'E': ['NEG', 'NEG', 'TOL', 'TOL'],
            'F': ['NEG', 'NEG', 'NEG', 'NEG']
        };

        var indexIS = {
            'IV': 0,
            'III': 1,
            'II': 2,
            'I': 3
        };

        if (table[IF] && indexIS.hasOwnProperty(IS)) {
            IR.value = table[IF][indexIS[IS]];
        } else {
            IR.value = 'Invalid Input';
        }
    }

</script>

@endsection
