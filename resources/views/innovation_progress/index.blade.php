@extends('layouts.universal')

@php
    use Carbon\Carbon;
@endphp

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="">Digital Inovation</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')  
    <div align="center">
        <div class="col-9">
            <div class="card card-outline card-danger">
                <div class="container">
                    <h1>Innovation Progress</h1>
                    <button class="btn btn-primary" onclick="showCreateModal()">Add New</button>

                    @php
                        $user = auth()->user();
                    @endphp

                    <table class="table mt-3">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Link Manual Book</th>
                                <th>Link Flowchart</th>
                                <th>Link Documentation</th>
                                @if($user->id == 1)
                                    <th>Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($innovations as $innovation)
                                <tr>
                                    <td>{{ $innovation->name }}</td>
                                    <td>{{ $innovation->description }}</td>
                                    <td>
                                        @if($innovation->manual_book_link)
                                            <a href="{{ $innovation->manual_book_link }}" target="_blank">Manual</a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($innovation->flow_chart_link)
                                            <a href="{{ $innovation->flow_chart_link }}" target="_blank">Flow Chart</a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($innovation->documentation_link)
                                            <a href="{{ $innovation->documentation_link }}" target="_blank">Docs</a>
                                        @endif
                                    </td>
                                    @if($user->id == 1)
                                        <td>
                                            <button class="btn btn-warning btn-sm"
                                                onclick="showEditModal({{ $innovation }})">Edit</button>
                                            <button class="btn btn-danger btn-sm"
                                                onclick="deleteInnovation({{ $innovation->id }})">Delete</button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('adminlte3/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script>
        // 🔹 MENAMPILKAN MODAL CREATE
        function showCreateModal() {
            Swal.fire({
                title: 'Add New Innovation',
                html: getCreateFormHtml(),
                showCancelButton: true,
                confirmButtonText: 'Save',
                preConfirm: () => saveNewInnovation()
            });
        }

        // 🔹 MENAMPILKAN MODAL EDIT
        function showEditModal(innovation) {
            Swal.fire({
                title: 'Edit Innovation',
                html: getEditFormHtml(innovation),
                showCancelButton: true,
                confirmButtonText: 'Update',
                preConfirm: () => updateInnovation(innovation.id)
            });
        }

        // 🔹 MENYIMPAN DATA BARU (CREATE)
        function saveNewInnovation() {
            let formData = {
                _token: '{{ csrf_token() }}',
                name: document.getElementById('create_name').value,
                description: document.getElementById('create_description').value,
                manual_book_link: document.getElementById('create_manual_book_link').value,
                flow_chart_link: document.getElementById('create_flow_chart_link').value,
                documentation_link: document.getElementById('create_documentation_link').value
            };

            Swal.fire({ title: 'Saving...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            $.ajax({
                url: "/innovation-progress/store",
                type: "POST",
                data: formData,
                success: function () {
                    Swal.fire('Success', 'Data saved successfully!', 'success').then(() => location.reload());
                },
                error: function () {
                    Swal.fire('Error', 'Something went wrong.', 'error');
                }
            });
        }

        // 🔹 MENGUPDATE DATA (EDIT)
        function updateInnovation(id) {
            let formData = {
                _token: '{{ csrf_token() }}', // Pastikan CSRF token tetap ada
                name: document.getElementById('edit_name').value,
                description: document.getElementById('edit_description').value,
                manual_book_link: document.getElementById('edit_manual_book_link').value,
                flow_chart_link: document.getElementById('edit_flow_chart_link').value,
                documentation_link: document.getElementById('edit_documentation_link').value
            };

            Swal.fire({ title: 'Updating...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            $.ajax({
                url: `/innovation-progress/update/${id}`,
                type: "PUT",  // ✅ Menggunakan PUT sesuai RESTful API
                data: formData,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, // ✅ Wajib untuk metode selain GET/POST
                success: function () {
                    Swal.fire('Success', 'Data updated successfully!', 'success').then(() => location.reload());
                },
                error: function () {
                    Swal.fire('Error', 'Something went wrong.', 'error');
                }
            });
        }



        // 🔹 HAPUS DATA
        function deleteInnovation(id) {
            Swal.fire({
                title: "Delete?",
                text: "Are you sure?",
                icon: "warning",
                showCancelButton: true
            }).then(result => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Deleting...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                    $.ajax({
                        url: `/innovation-progress/destroy/${id}`,
                        type: "DELETE",
                        data: { _token: '{{ csrf_token() }}' },
                        success: function () {
                            Swal.fire('Deleted!', 'Data has been deleted.', 'success').then(() => location.reload());
                        },
                        error: function () {
                            Swal.fire('Error', 'Failed to delete data.', 'error');
                        }
                    });
                }
            });
        }

        // 🔹 FORM CREATE
        function getCreateFormHtml() {
            return `
                            <input type="text" id="create_name" class="swal2-input" placeholder="Name" required>
                            <textarea id="create_description" class="swal2-textarea" placeholder="Description"></textarea>
                            <input type="url" id="create_manual_book_link" class="swal2-input" placeholder="Manual Book Link">
                            <input type="url" id="create_flow_chart_link" class="swal2-input" placeholder="Flow Chart Link">
                            <input type="url" id="create_documentation_link" class="swal2-input" placeholder="Documentation Link">
                        `;
        }

        // 🔹 FORM EDIT
        function getEditFormHtml(innovation) {
            return `
                            <input type="text" id="edit_name" class="swal2-input" placeholder="Name" value="${innovation.name}" required>
                            <textarea id="edit_description" class="swal2-textarea" placeholder="Description">${innovation.description}</textarea>
                            <input type="url" id="edit_manual_book_link" class="swal2-input" placeholder="Manual Book Link" value="${innovation.manual_book_link}">
                            <input type="url" id="edit_flow_chart_link" class="swal2-input" placeholder="Flow Chart Link" value="${innovation.flow_chart_link}">
                            <input type="url" id="edit_documentation_link" class="swal2-input" placeholder="Documentation Link" value="${innovation.documentation_link}">
                        `;
        }
    </script>
@endpush