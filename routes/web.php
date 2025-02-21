<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\MindMapController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DailyNotificationController;
use App\Http\Controllers\KatalogKomatController;
use App\Http\Controllers\TelegramMessagesAccountController;
use App\Http\Controllers\PDFEditorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobticketController;
use App\Http\Controllers\ZoomController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\WagroupnumberController;

use App\Http\Controllers\EkspedisiController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\NewBOMController;
use App\Http\Controllers\JustiMemoController;
use App\Http\Controllers\NewMemoController;
use App\Http\Controllers\OtomasiController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HazardLogController;
use App\Http\Controllers\NewreportController;
use App\Http\Controllers\BotTelegramController;
use App\Http\Controllers\ProjectTypeController;
use App\Http\Controllers\RamsDocumentFileController;
use App\Http\Controllers\NewprogressreportController;
use App\Http\Controllers\FileManagementController;
use App\Http\Controllers\UjiController;
use App\Http\Controllers\InnovationProgressController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;




Route::middleware('auth')->group(function () {



    Route::prefix('ekpedisi')->group(function () {
        Route::get('/', [EkspedisiController::class, 'index']);
        Route::post('/upload-pdf', [EkspedisiController::class, 'sendPdfToNode'])->name('ekpedisi.upload');

    });

    Route::prefix('innovation-progress')->group(function () {
        Route::get('/', [InnovationProgressController::class, 'index'])->name('innovation_progress.index');
        Route::post('/store', [InnovationProgressController::class, 'store'])->name('innovation_progress.store');

        Route::put('/update/{id}', [InnovationProgressController::class, 'update'])->name('innovation_progress.update');

        Route::delete('/destroy/{id}', [InnovationProgressController::class, 'destroy'])->name('innovation_progress.destroy');
    });






    // Route untuk mengunduh file DOCX
    Route::get('/download-file', function (Request $request) {
        $path = 'public/' . $request->query('path'); // Tambahkan 'public/' untuk akses file di storage/app/public

        // Pastikan file ada sebelum diunduh
        if (Storage::exists($path)) {
            return response()->download(Storage::path($path));
        }

        // Jika file tidak ditemukan, tampilkan error 404
        abort(404, 'File not found');
    })->name('download.file');

    Route::post('/reset-password/post', [AuthController::class, 'resetPassword'])->name('auth.resetpassword');
    Route::get('/reset-password', [AuthController::class, 'ResetForm']);

    Route::get('/', [HomeController::class, 'showHome']);
    Route::get('/slider', [HomeController::class, 'showHomeslider']);

    Route::get('/users/profile/{userId}', [AuthController::class, 'getUserLogs'])->name('user.logs');
    Route::get('/users', [AuthController::class, 'showAllUsers'])->name('all-users');


    Route::post('/set-internalon', [AuthController::class, 'setInternalOn'])->name('set.internalon');
    Route::post('/set-internalof', [AuthController::class, 'setInternalOff'])->name('set.internaloff');

    Route::put('/update-role/{user}', [AuthController::class, 'updateRole'])->name('update-role');
    Route::delete('/delete-user/{user}', [AuthController::class, 'deleteUser'])->name('delete-user');
    Route::get('/update-informasi', [AuthController::class, 'showUpdateForm'])->name('updateInformasiForm');
    Route::put('/update-informasi', [AuthController::class, 'updateInformasi'])->name('updateInformasi');
    Route::put('/update-password', [AuthController::class, 'updatePassword'])->name('updatePassword');

    Route::post('/update-ttd', [AuthController::class, 'updatettd'])->name('updatettd');

    Route::get('/massuploaduser', [FileController::class, 'massuploaduser']);
    Route::post('/massuploaduser', [FileController::class, 'uploadmassuploaduser']);

    Route::get('/search-results', [FileController::class, 'searchMetadata'])->name('searchresult');
    Route::get('/search', [FileController::class, 'searchForm'])->name('searchview');

    Route::get('/file/aksi/upload', [FileController::class, 'showuploadfile']);
    Route::post('/file/aksi/upload', [FileController::class, 'postuploadfile'])->name('file.upload');


    Route::get('/file/{id}', [FileController::class, 'showMetadata'])->name('metadata.show');
    Route::get('/file/{id}/edit', [FileController::class, 'metadataedit'])->name('metadata.edit');
    Route::put('/file/{id}', [FileController::class, 'updateinformasimetadata'])->name('file.update');
    Route::get('/file', [FileController::class, 'showAllMetadata'])->name('metadata.all');
    Route::delete('/files/{id}', [FileController::class, 'deleteFile'])->name('file.delete');
    Route::delete('/file/deletefileMultiple', [FileController::class, 'deleteFileMultiple'])->name('file.deleteMultiple');

    Route::post('/document/deletedocumentMultiple', [FileController::class, 'deleteDocumentMultiple'])->name('document.deleteMultiple');
    Route::post('/document/reportMultiple', [FileController::class, 'reportDocumentMultiple'])->name('document.reportMultiple');

    Route::get('/previewdocument/{linkfile}', [FileController::class, 'previewDocument'])->name('document.preview');
    Route::get('/download/{id}', [FileController::class, 'downloadFile'])->name('file.download');

    Route::get('/document', [FileController::class, 'showAllDocument'])->name('document.all');

    Route::post('/document/memo/upload', [FileController::class, 'uploadDocMTPR'])->name('documentMTPR.upload');


    Route::get('/document/memo/upload', [FileController::class, 'uploadForm']);
    Route::get('/document/memo/massupload', [FileController::class, 'ShowUploadDocMTPRExcell'])->name('users.export');
    Route::post('/document/memo/massupload', [FileController::class, 'uploadDocMTPRExcel'])->name('users.import');
    Route::get('/document/memo/{id}', [FileController::class, 'showDocument'])->name('memo.show');
    Route::put('/document/memo/{id}/senddecision', [FileController::class, 'sendDecision'])->name('senddecision.Document');
    Route::put('/document/memo/{id}/deletedfeedbackdecision/{sendtime}', [FileController::class, 'deletedFeedbackDecision'])->name('deletedfeedbackdecision.Document');
    Route::put('/document/memo/{id}/unsenddecision/{sendtime}', [FileController::class, 'unsendDecision'])->name('unsenddecision.Document');
    Route::get('/document/memo/{idmemo}/allert', [FileController::class, 'showDocument'])->name('allert.show');
    Route::get('/document/memo/{id}/edit', [FileController::class, 'memoedit'])->name('document.edit');
    Route::get('/document/memo/{id}/delete', [FileController::class, 'documentdelete'])->name('document.delete');
    Route::get('/document/memo/{id}/uploadfeedback', [FileController::class, 'documentfeedback'])->name('document.uploadfeedback');
    Route::get('/document/memo/{id}/uploadmanagerfeedback', [FileController::class, 'documentmanagerfeedback'])->name('document.uploadmanagerfeedback');

    Route::get('/document/memo/{id}/uploadcombine', [FileController::class, 'documentcombine'])->name('document.uploadcombine');
    Route::put('/document/memo/{id}/uploadmanagerfeedback', [FileController::class, 'uploadsignaturefeedbackmerge'])->name('upload.ManagerFeedback');
    Route::put('/document/memo/{id}/uploadfeedback', [FileController::class, 'uploadsignaturefeedbackmerge'])->name('upload.Feedback');

    Route::get('/document/memo/{id}/uploadsignature', [FileController::class, 'documentsignature'])->name('document.uploadsignature');
    Route::put('/document/memo/{id}/uploadsignature', [FileController::class, 'uploadsignaturefeedbackmerge'])->name('upload.Signature');

    Route::put('/document/memo/{id}/uploadcombine', [FileController::class, 'uploadsignaturefeedbackmerge'])->name('upload.Combine');
    Route::put('/document/memo/{id}', [FileController::class, 'updateinformasimemo'])->name('edit.Document');
    Route::put('/document/memo/{id}/sendfowardDocument', [FileController::class, 'sendfowardDocument'])->name('sendfoward.Document');
    Route::get('/document/memo/{id}/progress', [FileController::class, 'ReportDocument'])->name('document.report');
    Route::post('/document/memo/{id}/destroy', [FileController::class, 'destroydocument'])->name('documents.destroy');
    Route::get('/document/memo/{id}/destroyget', [FileController::class, 'destroydocument']);
    Route::get('/document/memo/all/mapping', [FileController::class, 'mappingAllDocument'])->name('mapping.all');
    Route::get('/document/memo/{id}/pdf', [FileController::class, 'mappingpersonalDocument']);
    Route::get('/document/memo/{id}/pdfdownload', [FileController::class, 'mappingpersonalDocumentdownload']);
    Route::put('/document/memo/{id}/update-document-status/', [FileController::class, 'updateStatus'])->name('update.document.status');
    // Untuk menggunakan method GET
    Route::get('/komat/update/{id}/{index}', [FileController::class, 'updatekomat']);
    Route::get('/komat/delete/{id}/{index}', [FileController::class, 'deletekomat']);
    // Untuk menggunakan method POST (jika lebih sesuai)
    Route::post('/komat/update/{id}/{index}', [FileController::class, 'updatekomat']);


    // Route untuk menampilkan form input kategori
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    // Route untuk menyimpan kategori baru
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    // Route untuk menampilkan semua kategori
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::delete('/categories/{categoryId}/members/{memberId}', [CategoryController::class, 'destroyMember'])->name('members.destroy');
    Route::post('/categories/{categoryId}/members', [CategoryController::class, 'storeMember'])->name('members.store');
    Route::get('/showsistem', [FileController::class, 'main2']);
    Route::get('ujicobabot', [BotTelegramController::class, 'informasichatbot']);



    Route::get('/newreports', [NewreportController::class, 'index'])->name('newreports.index');
    Route::get('/newreports/slideshow', [NewreportController::class, 'indexslideshow'])->name('newreports.indexslideshow');




    Route::post('/newreports/update-documentnumber', [NewreportController::class, 'updateDocumentNumber'])->name('newreports.updateDocumentNumber');


    Route::get('/newreports/calculatelastpercentage', [NewreportController::class, 'calculatelastpercentage'])->name('newreports.calculatelastpercentage');
    Route::get('/newreports/indexlogpercentage', [NewreportController::class, 'indexlogpercentage'])->name('newreports.indexlogpercentage');
    Route::get('/newreports/{newreport}/showlog/{logid}', [NewreportController::class, 'showlog'])->name('newreports.showlog');
    Route::get('/newreports/{newreport}', [NewreportController::class, 'show'])->name('newreports.show');
    Route::get('/newreports/{newreport}/{id}', [NewreportController::class, 'showrev'])->name('newreports.showrev');
    Route::get('/laporan/{newreport}', [NewreportController::class, 'laporan'])->name('newreports.laporan');


    Route::get('/newreports/{newreport}/doubledetector', [NewreportController::class, 'doubledetector'])->name('newreports.doubledetector');
    Route::post('/newreports/{newreport}/download', [NewreportController::class, 'downloadprogress'])->name('newreports.download');
    Route::post('/newreports/{newreport}/downloadbyproject', [NewreportController::class, 'downloadprogressbyproject'])->name('newreports.downloadbyproject');
    Route::get('/newreports/{newreport}/viewbyprojectprogress', [NewreportController::class, 'viewbyprojectprogress'])->name('newreports.viewbyprojectprogress');
    Route::post('/newreports/{newreport}/downloadlaporan', [NewreportController::class, 'downloadlaporan'])->name('newreports.downloadlaporan');


    Route::post('/newreports/{newreport}/downloadduplicatebyproject', [NewreportController::class, 'downloadduplicatebyproject'])->name('newreports.downloadduplicatebyproject');
    Route::delete('/newreports/{newreport}/destroy', [NewreportController::class, 'destroy'])->name('newreports.destroy');
    Route::delete('/newreports/{newreport}/destroydian', [NewreportController::class, 'destroydian'])->name('newreports.destroydian');
    Route::get('/newreports/{newreport}/progressreports/create', [NewprogressreportController::class, 'create'])->name('newprogressreports.create');
    Route::post('/newreports/{newreport}/progressreports', [NewprogressreportController::class, 'store'])->name('newprogressreports.store');




    Route::prefix('newprogressreports')->group(function () {

        Route::post('/update-document-kind-progress', [NewprogressreportController::class, 'updateDocumentKind'])
            ->name('newprogressreports.updateDocumentKind');



        Route::get('/', [NewprogressreportController::class, 'index'])->name('newprogressreports.index');
        Route::post('/{newprogressreport}/delete', [NewprogressreportController::class, 'destroy']);
        Route::get('/{newprogressreport}/detail', [NewprogressreportController::class, 'detail']);
        Route::post('/picktugas/{id}/{name}', [NewprogressreportController::class, 'picktugas'])->name('newprogressreports.picktugas');
        Route::post('/starttugas/{id}/{name}', [NewprogressreportController::class, 'starttugas'])->name('newprogressreports.starttugas');
        Route::post('/pausetugas/{id}/{name}', [NewprogressreportController::class, 'pausetugas'])->name('newprogressreports.pausetugas');
        Route::post('/resumetugas/{id}/{name}', [NewprogressreportController::class, 'resumetugas'])->name('newprogressreports.resumetugas');
        Route::post('/selesaitugas/{id}/{name}', [NewprogressreportController::class, 'selesaitugas'])->name('newprogressreports.selesaitugas');
        Route::post('/resettugas/{id}/{name}', [NewprogressreportController::class, 'resettugas'])->name('newprogressreports.resettugas');
        Route::post('/unlinkparent/{id}/', [NewprogressreportController::class, 'unlinkparent'])->name('newprogressreports.unlinkparent');
        Route::post('/izinkanrevisitugas/{id}/{name}', [NewprogressreportController::class, 'izinkanrevisitugas'])->name('newprogressreports.izinkanrevisitugas');
        Route::post('/updateprogressreport/{id}/', [NewprogressreportController::class, 'updateprogressreport'])->name('newprogressreports.updateprogressreport');
        Route::get('/document-kind', [NewprogressreportController::class, 'indexdokumentkind'])->name('newprogressreports.document-kindindex');
        Route::post('/document-kind', [NewprogressreportController::class, 'storedokumentkind'])->name('newprogressreports.document-kindstore');
        Route::get('/search', [NewProgressReportController::class, 'showSearchForm'])->name('newprogressreports.searchform');
        Route::get('/search/results', [NewProgressReportController::class, 'search'])->name('newprogressreports.search');
        Route::post('/newprogressreports/uploadsistemku/', [NewprogressreportController::class, 'importExcelsistem'])->name('newprogressreports.updateexcel');
        Route::get('/upload', [NewprogressreportController::class, 'showUploadFormExcel']);
        Route::post('/handleDeleteMultipleItems', [NewprogressreportController::class, 'handleDeleteMultipleItems'])->name('newprogressreports.handleDeleteMultipleItems');

        Route::post('/handleReleaseMultipleItems', [NewprogressreportController::class, 'handleReleaseMultipleItems'])->name('newprogressreports.handleReleaseMultipleItems');
        Route::post('/handleUnreleaseMultipleItems', [NewprogressreportController::class, 'handleUnreleaseMultipleItems'])->name('newprogressreports.handleUnreleaseMultipleItems');
        Route::get('/notif-harian-units', [NewprogressreportController::class, 'indexnotifharian'])->name('newprogressreports.index-notif-harian-units');
        Route::post('/notif-harian-units', [NewprogressreportController::class, 'storenotifharian'])->name('newprogressreports.store-notif-harian-units');
        Route::get('/notif-harian-units/{id}/edit', [NewprogressreportController::class, 'editnotifharian'])->name('newprogressreports.edit-notif-harian-unit');
        Route::post('/notif-harian-units/{id}', [NewprogressreportController::class, 'updatenotifharian'])->name('newprogressreports.update-notif-harian-unit');
        Route::delete('/notif-harian-units/{id}', [NewprogressreportController::class, 'deletenotifharian'])->name('newprogressreports.delete-notif-harian-unit');

        Route::get('/today', [NewprogressreportController::class, 'today']);


    });



    // zoom delete
    Route::get('/auth/{account_name}/zoomverify', [ZoomController::class, 'handleZoomCallback']);
    Route::get('/redirectzoom/{account_name}/', [ZoomController::class, 'redirectToZoom'])->name('zoom.auth');
    Route::get('/delete-meeting/{account_name}/{meetingId}', [ZoomController::class, 'deleteMeeting'])->name('meeting.delete.form');
    Route::delete('/delete-meeting/{account_name}/{meetingId}', [ZoomController::class, 'deleteMeeting'])->name('meeting.delete');
    Route::get('/zoom', [ZoomController::class, 'index'])->name('zoom.index');
    Route::post('/zoom', [ZoomController::class, 'store'])->name('zoom.store');
    Route::get('/zoom/create', [ZoomController::class, 'create'])->name('zoom.create');
    Route::get('/zoom/{id}', [ZoomController::class, 'show'])->name('zoom.show');
    Route::delete('/zoom/{id}', [ZoomController::class, 'destroy'])->name('zoom.destroy');


    Route::post('/zoom/{id}/update', [ZoomController::class, 'update'])->name('zoom.update');
    Route::post('/zoom/delete-multiple', [ZoomController::class, 'deleteMultiple'])->name('zoom.deleteMultiple');


    Route::get('/daily-notifications', [DailyNotificationController::class, 'index'])->name('daily-notifications.index');
    Route::get('/daily-notifications/show/{id}', [DailyNotificationController::class, 'show'])->name('daily-notifications.show');
    Route::get('/daily-notifications/download/{id}', [DailyNotificationController::class, 'downloadpdf'])->name('daily-notifications.downloadpdf');


    Route::prefix('library')->group(function () {
        // Route GET untuk menampilkan semua file
        Route::get('/', [FileManagementController::class, 'index'])->name('library.index');

        // Route GET untuk menampilkan form unggah file baru
        Route::get('/create', [FileManagementController::class, 'create'])->name('library.create');

        // Route POST untuk menyimpan file baru
        Route::post('/', [FileManagementController::class, 'store'])->name('library.store');

        // Route GET untuk menampilkan form edit file berdasarkan ID
        Route::get('/{id}/edit', [FileManagementController::class, 'edit'])->name('library.edit');

        // Route POST untuk memperbarui file yang ada
        Route::post('/{id}', [FileManagementController::class, 'update'])->name('library.update');

        // Route POST untuk menghapus file berdasarkan ID
        Route::delete('/{id}/delete', [FileManagementController::class, 'destroy'])->name('library.destroy');



    });


    Route::prefix('katalogkomat')->group(function () {
        Route::post('/katalogkomat/uploadsistemku/', [KatalogKomatController::class, 'importExcelsistem'])->name('katalogkomat.excel');
        Route::get('/uploadexcel', [KatalogKomatController::class, 'showUploadForm'])->name('katalogkomat.formexcel');
        Route::get('/', [KatalogKomatController::class, 'index'])->name('katalogkomat.index');
        Route::get('/data', [KatalogKomatController::class, 'getData'])->name('katalogkomat.getData');
    });

    // Rute untuk Hazard Logs
    Route::prefix('hazard_logs')->group(function () {
        Route::get('/', [HazardLogController::class, 'index'])->name('hazard_logs.index');
        Route::post('/', [HazardLogController::class, 'store'])->name('hazard_logs.store');
        Route::get('/create', [HazardLogController::class, 'create'])->name('hazard_logs.create');
        Route::put('/{id}', [HazardLogController::class, 'update'])->name('hazard_logs.update');
        Route::delete('/{id}', [HazardLogController::class, 'destroy'])->name('hazard_logs.destroy');
        Route::get('{id}/show/', [HazardLogController::class, 'show'])->name('hazard_logs.show');
        Route::get('/{id}/edit', [HazardLogController::class, 'edit'])->name('hazard_logs.edit');
        Route::get('/{id}/{level}/feedback', [HazardLogController::class, 'viewfeedback'])->name('hazard_logs.feedback');
        Route::get('/{id}/{level}/combine', [HazardLogController::class, 'viewcombine'])->name('hazard_logs.combine');

        Route::post('/{id}/feedback', [HazardLogController::class, 'submitFeedback'])->name('hazard_logs.submitFeedback');
        Route::delete('/{hazardLogId}/feedback/{feedbackId}', [HazardLogController::class, 'destroyFeedback'])->name('hazard_logs.feedback.destroy');
        Route::post('/{hazardLogId}/approve/{feedbackId}', [HazardLogController::class, 'approveFeedback'])->name('hazard_logs.feedback.approve');
        Route::post('/{hazardLogId}/reject/{feedbackId}', [HazardLogController::class, 'rejectFeedback'])->name('hazard_logs.feedback.reject');

        Route::post('/{hazardLogId}/deletestatus/', [HazardLogController::class, 'deletestatus'])->name('hazard_logs.deletestatus');


        Route::post('/{hazardLogId}/approvehazardlog/{reductionMeasureId}', [HazardLogController::class, 'approvehazardlog'])->name('hazard_logs.hazardlog.approve');
        Route::post('/{hazardLogId}/rejecthazardlog/{reductionMeasureId}', [HazardLogController::class, 'rejecthazardlog'])->name('hazard_logs.hazardlog.reject');
        Route::post('/{hazardLogId}/addhazardlog/{unit_name}', [HazardLogController::class, 'addhazardlog'])->name('hazard_logs.hazardlog.add');
        Route::post('/{hazardLogId}/makeforum/{reductionMeasureId}', [HazardLogController::class, 'makeforum'])->name('hazard_logs.hazardlog.makeforum');

    });

    // Rute untuk RAMS
    Route::prefix('rams')->group(function () {
        Route::get('', [RamsDocumentFileController::class, 'index'])->name('ramsdocuments.index');
        Route::get('/create', [RamsDocumentFileController::class, 'create'])->name('ramsdocuments.create');
        Route::post('/', [RamsDocumentFileController::class, 'storeDocument'])->name('ramsdocuments.store');
        Route::get('/{document}', [RamsDocumentFileController::class, 'show'])->name('ramsdocuments.show');
        Route::get('/{document}/edit', [RamsDocumentFileController::class, 'edit'])->name('ramsdocuments.edit');
        Route::put('/{document}', [RamsDocumentFileController::class, 'update'])->name('ramsdocuments.update');
        Route::delete('/{document}', [RamsDocumentFileController::class, 'destroy'])->name('ramsdocuments.destroy');
        Route::get('/{id}/{level}/feedback', [RamsDocumentFileController::class, 'viewfeedback'])->name('ramsdocuments.feedback');
        Route::get('/{id}/{level}/combine', [RamsDocumentFileController::class, 'viewcombine'])->name('ramsdocuments.combine');
        Route::get('/{id}/{level}/smfeedback', [RamsDocumentFileController::class, 'viewsmfeedback'])->name('ramsdocuments.smfeedback');
        Route::get('/{id}/{level}/finalisasi', [RamsDocumentFileController::class, 'viewfinalisasi'])->name('ramsdocuments.finalisasi');
        Route::post('/{id}/feedbackcombine', [RamsDocumentFileController::class, 'submitFeedbackCombine'])->name('ramsdocuments.submitFeedbackCombine');
        Route::delete('/{documentId}/feedback/{feedbackId}', [RamsDocumentFileController::class, 'destroyFeedback'])->name('ramsdocuments.feedback.destroy');
        Route::post('/{documentId}/approve/{feedbackId}', [RamsDocumentFileController::class, 'approveFeedback'])->name('ramsdocuments.feedback.approve');
        Route::post('/{documentId}/reject/{feedbackId}', [RamsDocumentFileController::class, 'rejectFeedback'])->name('ramsdocuments.feedback.reject');
        Route::get('/{id}/sendSM', [RamsDocumentFileController::class, 'sendSM'])->name('ramsdocuments.sendSM');
    });

    // Rute untuk Forum
    Route::prefix('forums')->group(function () {
        Route::get('/', [ForumController::class, 'index'])->name('forums.index');
        Route::get('/show/{forum}', [ForumController::class, 'show'])->name('forums.show');
        Route::get('/create', [ForumController::class, 'create'])->name('forums.create');
        Route::post('/', [ForumController::class, 'store'])->name('forums.store');
        Route::post('/{forum}/chats', [ForumController::class, 'storeChat'])->name('forums.chats.store');
        Route::get('/{id}/chats', [ForumController::class, 'loadChats'])->name('forums.chats.load');
        Route::get('/{id}/newchats', [ForumController::class, 'loadNewChats'])->name('forums.newChats');


    });

    // Rute untuk Newbom
    Route::prefix('newboms')->group(function () {
        Route::get('/', [NewBOMController::class, 'indexNewbom'])->name('newbom.index');
        Route::post('/', [NewBOMController::class, 'storeNewbom']);
        Route::get('/show/{id}', [NewBOMController::class, 'showNewbom'])->name('newbom.show');
        Route::post('/download/{id}', [NewBomController::class, 'downloadbom'])->name('newbom.downloadbom');


        Route::delete('/show/{id}', [NewBOMController::class, 'destroyNewbom']);
        Route::get('/uploadexcel', [NewBOMController::class, 'showUploadForm'])->name('uploadnewbom.form');
        Route::post('/exportexcel', [NewBOMController::class, 'importExcelsistem'])->name('importnewbom.excel');
        Route::get('/logpercentage', [NewBOMController::class, 'indexlogpercentage'])->name('newboms.indexlogpercentage');
        Route::get('/create', [NewBOMController::class, 'createNewbom'])->name('newbom.create');

        Route::get('/newbomkomats/{id}', [NewBOMController::class, 'storeNewbomkomat']);
        Route::post('/newbomkomats/{id}/{idkomat}', [NewBOMController::class, 'changeNewbomkomat']);
        Route::post('/newbomkomats/{id}/{idkomat}/delete', [NewBOMController::class, 'deleteNewbomkomat']);

        Route::post('/operatorfindbykomat', [NewBOMController::class, 'operatorfindbykomat'])->name('newbom.operatorfindbykomat');
        Route::get('/search', [NewBOMController::class, 'searchkomat'])->name('newbom.searchkomat');


    });

    // Rute untuk Nodokumen
    Route::prefix('new-memo')->group(function () {

        Route::get('/terbuka', [NewMemoController::class, 'indexterbuka'])->name('new-memo.index');
        Route::get('/tertutup', [NewMemoController::class, 'indextertutup'])->name('new-memo.indextertutup');
        Route::get('/roadmap/{memoId}', [NewMemoController::class, 'roadmap'])->name('new-memo.roadmap');
        Route::get('/lastfile/{memoId}', [NewMemoController::class, 'downloadfilesfromlastfeedback'])->name('new-memo.downloadfilesfromlastfeedback');
        Route::get('/timelinetracking/{memoId}', [NewMemoController::class, 'timelinetracking'])->name('new-memo.timelinetracking');


        Route::put('/show/{memoId}', [NewMemoController::class, 'updateinformasimemo'])->name('new-memo.posteditdocument');
        Route::get('/show/{memoId}/edit', [NewMemoController::class, 'memoedit'])->name('new-memo.edit');
        Route::get('/show/{memoId}', [NewMemoController::class, 'showDocument'])->name('new-memo.show');
        Route::post('/show/{memoId}/feedback', [NewMemoController::class, 'addFeedback'])->name('new-memo.addFeedback');
        Route::post('/show/{memoId}/komat', [NewMemoController::class, 'addKomat'])->name('new-memo.addKomat');
        Route::get('/show/{memoId}/uploadsignature', [NewMemoController::class, 'documentsignature'])->name('new-memo.uploadsignature');
        Route::put('/show/{memoId}/uploadsignature', [NewMemoController::class, 'uploadsignaturefeedbackmerge'])->name('new-memo.allfeedback');
        Route::get('/show/{memoId}/edit', [NewMemoController::class, 'memoedit'])->name('new-memo.edit');
        Route::put('/show/{memoId}/uploadcombine', [NewMemoController::class, 'uploadsignaturefeedbackmerge'])->name('new-memo.Combine');
        Route::get('/show/{memoId}/uploadfeedback', [NewMemoController::class, 'documentfeedback'])->name('new-memo.uploadfeedback');
        Route::put('/show/{memoId}/sendfeedback', [NewMemoController::class, 'uploadsignaturefeedbackmerge'])->name('new-memo.sendfeedback');
        Route::put('/show/{memoId}/senddecision', [NewMemoController::class, 'sendDecision'])->name('new-memo.senddecision');
        Route::put('/show/{memoId}/sendfowardDocument', [NewMemoController::class, 'sendfowardDocument'])->name('new-memo.sendfoward');
        Route::put('/show/{memoId}/deletedfeedbackdecision', [NewMemoController::class, 'deletedFeedbackDecision'])->name('new-memo.deletedfeedbackdecision');
        Route::get('/show/{memoId}/uploadmanagerfeedback', [NewMemoController::class, 'documentmanagerfeedback'])->name('new-memo.uploadmanagerfeedback');
        Route::get('/show/{memoId}/uploadcombine', [NewMemoController::class, 'documentcombine'])->name('new-memo.uploadcombine');
        Route::put('/show/{memoId}/unsenddecision', [NewMemoController::class, 'unsendDecision'])->name('new-memo.unsenddecision');
        Route::post('/show/{memoId}/updatedocumentstatus/', [NewMemoController::class, 'updateStatus'])->name('new-memo.updatedocumentstatus');

        Route::post('/upload', [NewMemoController::class, 'uploadDocMTPR'])->name('new-memo.upload');
        Route::get('/upload', [NewMemoController::class, 'uploadForm']);

        Route::get('/migrasimemonewmemo', [NewMemoController::class, 'migrasimemonewmemo']);
        Route::get('/migrasimemonewmemoefesien', [NewMemoController::class, 'migrasimemonewmemoefesien']);
        Route::get('/migrasimemonewmemoefesienbyproject', [NewMemoController::class, 'migrasimemonewmemoefesienbyproject']);

        Route::get('/show/all/leadtimeperunit', [NewMemoController::class, 'leadtimeperunit']);

        Route::get('/indexterbukayajra', [NewMemoController::class, 'indexterbukayajra'])->name('new-memo.indexterbukayajra');

        Route::post('/download', [NewMemoController::class, 'newmemodownload'])->name('new-memo.download');
        Route::get('/downloadall', [NewMemoController::class, 'newmemodownloadall'])->name('new-memo.downloadall');

    });

    // Rute untuk Nodokumen
    Route::prefix('justi-memo')->group(function () {
        Route::get('/upload', [JustiMemoController::class, 'uploadForm']);
        Route::post('/upload', [JustiMemoController::class, 'uploadDocMTPR'])->name('justi-memo.upload');
        Route::get('/show/{memoId}', [JustiMemoController::class, 'showDocument'])->name('justi-memo.show');
        Route::get('/show/{memoId}/uploadfeedback', [JustiMemoController::class, 'documentfeedback'])->name('justi-memo.uploadfeedback');
    });

    // Rute untuk Project
    Route::prefix('project_types')->group(function () {
        // Menampilkan daftar project types (Index)
        Route::get('/', [ProjectTypeController::class, 'index'])->name('project_types.index');

        // Menampilkan form untuk membuat project type baru (Create)
        Route::get('/create', [ProjectTypeController::class, 'create'])->name('project_types.create');

        // Menyimpan project type baru (Store)
        Route::post('/store', [ProjectTypeController::class, 'store'])->name('project_types.store');

        Route::get('/data', [ProjectTypeController::class, 'data'])->name('project_types.data');

        // Menampilkan detail dari project type tertentu (Show)
        Route::get('/{project_type}', [ProjectTypeController::class, 'show'])->name('project_types.show');

        // Menampilkan form untuk mengedit project type tertentu (Edit)
        Route::get('/{project_type}/edit', [ProjectTypeController::class, 'edit'])->name('project_types.edit');

        // Memperbarui project type tertentu (Update)
        Route::post('/{project_type}', [ProjectTypeController::class, 'update'])->name('project_types.update');

        // Menghapus project type tertentu (Destroy)
        Route::post('/{project_type}/delete', [ProjectTypeController::class, 'destroy'])->name('project_types.destroy');
    });

    // Rute untuk Unit
    Route::prefix('unit')->group(function () {
        // Menampilkan daftar project types (Index)
        Route::get('/', [UnitController::class, 'index'])->name('unit.index');

        // Menampilkan form untuk membuat project type baru (Create)
        Route::get('/create', [UnitController::class, 'create'])->name('unit.create');

        // Menyimpan project type baru (Store)
        Route::post('/store', [UnitController::class, 'store'])->name('unit.store');

        Route::get('/data', [UnitController::class, 'data'])->name('unit.data');

        // Menampilkan detail dari project type tertentu (Show)
        Route::get('/{unit}', [UnitController::class, 'show'])->name('unit.show');

        // Menampilkan form untuk mengedit project type tertentu (Edit)
        Route::get('/{unit}/edit', [UnitController::class, 'edit'])->name('unit.edit');

        // Memperbarui project type tertentu (Update)
        Route::post('/{unit}', [UnitController::class, 'update'])->name('unit.update');

        // Menghapus project type tertentu (Destroy)
        Route::post('/{unit}/delete', [UnitController::class, 'destroy'])->name('unit.destroy');
    });

    // Rute untuk Telegram
    Route::prefix('telegram-messages-accounts')->group(function () {
        Route::get('/', [TelegramMessagesAccountController::class, 'index'])->name('telegram_messages_accounts.index');
        Route::post('/', [TelegramMessagesAccountController::class, 'store'])->name('telegram_messages_accounts.store');
        Route::get('/create', [TelegramMessagesAccountController::class, 'create'])->name('telegram_messages_accounts.create');
        Route::get('/show/{telegramMessagesAccount}', [TelegramMessagesAccountController::class, 'show'])->name('telegram_messages_accounts.show');
        Route::get('/edit/{telegramMessagesAccount}/edit', [TelegramMessagesAccountController::class, 'edit'])->name('telegram_messages_accounts.edit');
        Route::put('/update/{telegramMessagesAccount}', [TelegramMessagesAccountController::class, 'update'])->name('telegram_messages_accounts.update');
        Route::delete('/destroy/{telegramMessagesAccount}', [TelegramMessagesAccountController::class, 'destroy'])->name('telegram_messages_accounts.destroy');
    });

    // Rute untuk Notification
    Route::prefix('notification/')->group(function () {
        Route::get('/receive/{namadivisi}', [NotificationController::class, 'showByDivisi'])->name('notification.show');
        Route::post('/sendwa', [NotificationController::class, 'sendwa'])->name('notification.sendwa');
        Route::get('/viewsendwa', [NotificationController::class, 'viewsendwa'])->name('notification.viewsendwa');
    });

    //Ruang Rapat
    Route::prefix('events')->group(function () {
        Route::get('/all', [EventController::class, 'index'])->name('events.all');

        Route::get('/rooms/{room}', [EventController::class, 'room'])->name('events.room');
        Route::get('/edit/{id}', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/update/{id}', [EventController::class, 'update'])->name('events.update');
        Route::get('/create', [EventController::class, 'create'])->name('events.create');
        Route::get('/listMeetingParticipants/{id}', [EventController::class, 'listMeetingParticipants'])->name('events.listMeetingParticipants');


        Route::post('/check/roomavailability', [EventController::class, 'checkRoomAvailability'])->name('checkRoomAvailability');
    });


    //Ruang Rapat
    Route::prefix('jobticket')->group(function () {
        Route::put('/jobticket-identity/{id}/update-documentname', [JobticketController::class, 'updateDocumentName'])->name('jobticket.updateDocumentName');


        Route::post('/downloadzip', [JobticketController::class, 'downloadjobticket'])
            ->name('jobticket.downloadZIP');

        Route::post('/downloadexcel', [JobticketController::class, 'downloadexcel'])
            ->name('jobticket.downloadexcel');

        Route::get('/downloadWLA', [JobticketController::class, 'downloadWLA'])->name('jobticket.downloadWLA');

        Route::get('/unit', [JobticketController::class, 'showunit'])->name('jobticket.showunit');



        Route::get('/jobticket-document-kind', [JobticketController::class, 'indexjobticketdokumentkind'])->name('jobticket.jobticket-document-kindindex');
        Route::post('/jobticket-document-kind', [JobticketController::class, 'storejobticketdokumentkind'])->name('jobticket.jobticket-document-kindstore');

        Route::post('/jobticket-released/{id}', [JobticketController::class, 'releasedDocument'])->name('jobticket.released');

        Route::get('/', [JobticketController::class, 'index'])->name('jobticket.index');
        Route::get('/show/{id}', [JobticketController::class, 'show'])->name('jobticket.show');
        Route::get('/show/{id}/{iddocumentnumber}', [JobticketController::class, 'showdocument'])->name('jobticket.showdocument');
        Route::put('/updatesupportdocument/{jobticketid}', [JobticketController::class, 'updatesupportdocument'])->name('jobticket.updatesupportdocument');

        Route::get('/showself/terbuka', [JobticketController::class, 'showdocumentselfterbuka'])->name('jobticket.showdocumentselfterbuka');
        Route::get('/showself/tertutup', [JobticketController::class, 'showdocumentselftertutup'])->name('jobticket.showdocumentselftertutup');

        Route::get('/manager/terbuka', [JobticketController::class, 'managershow'])->name('jobticket.managershow');

        Route::get('/showmember/{id}/{status}', [JobticketController::class, 'showdocumentmember'])->name('jobticket.showdocumentmember');

        Route::get('/show/{jobticket_identity_part}/{jobticket_identity_id}/{jobticket_id}', [JobticketController::class, 'detail'])->name('jobticket.detail');


        Route::post('/statushistory/{id}/mark-as-read', [JobticketController::class, 'markAsRead'])->name('jobticket.markAsRead');


        Route::get('/deletejobticket/{jobticket_identity_part}/{jobticket_identity_id}/{jobticket_id}', [JobticketController::class, 'deletejobticket'])->name('jobticket.deletejobticket');

        Route::get('/close/{jobticket_identity_part}/{jobticket_identity_id}/{jobticket_id}', [JobticketController::class, 'close'])->name('jobticket.close');
        Route::get('/uploadexcel', [JobticketController::class, 'showUploadFormExcel'])->name('jobticket.uploadexcel');
        Route::post('/uploadsistemku/', [JobticketController::class, 'importExcelsistem'])->name('jobticket.updateexcel');

        Route::post('/picknote/{id}', [JobticketController::class, 'picknote'])->name('jobticket.picknote');
        Route::post('/pickdraftercheckerapprover/{id}', [JobticketController::class, 'pickdraftercheckerapprover'])->name('jobticket.pickdraftercheckerapprover');
        Route::post('/jobticketstartedrev/pickdraftercheckerapprover/{id}', [JobticketController::class, 'jobticketstartedrevpickdraftercheckerapprover'])->name('jobticket.jobticketstartedrevpickdraftercheckerapprover');

        Route::put('/update-revision/{id}', [JobticketController::class, 'updateRevision']);

        Route::post('/picktugas/{id}/{name}/{kindposition}', [JobticketController::class, 'picktugas'])->name('jobticket.picktugas');
        Route::post('/starttugas/{id}/{name}', [JobticketController::class, 'starttugas'])->name('jobticket.starttugas');
        Route::post('/pausetugas/{id}/{name}', [JobticketController::class, 'pausetugas'])->name('jobticket.pausetugas');

        Route::post('/reasontugas/', [JobticketController::class, 'reasontugas'])->name('jobticket.reasontugas');

        Route::post('/resumetugas/{id}/{name}', [JobticketController::class, 'resumetugas'])->name('jobticket.resumetugas');
        Route::post('/selesaitugas/{id}/{name}', [JobticketController::class, 'selesaitugas'])->name('jobticket.selesaitugas');

        Route::post('/izinkanrevisitugas/{id}/{name}', [JobticketController::class, 'izinkanrevisitugas'])->name('jobticket.izinkanrevisitugas');
        Route::post('/approveperbaikan/{revision}/{kindposition}', [JobticketController::class, 'revisionapprove'])->name('jobticket.revisionapprove');


        Route::post('/reminder/{revision}/{kindposition}', [JobticketController::class, 'reminder'])->name('jobticket.reminder');



        Route::put('/revisionapprovedoc/{revision}/{kindposition}', [JobticketController::class, 'revisionapprovedoc'])->name('jobticket.revisionapprovedoc');
        Route::get('/uploadverifikasi/{revision}', [JobticketController::class, 'uploadverifikasi'])->name('jobticket.uploadverifikasi');
        Route::delete('/deleterevision/{revision}', [JobticketController::class, 'deleteRevision'])->name('jobticket.deleterevision');

        Route::get('/rank', [JobticketController::class, 'rank'])->name('jobticket.rank');

        Route::get('/unfinished', [JobticketController::class, 'unfinished'])->name('jobticket.unfinished');

        Route::post('/jobticket-add-document', [JobticketController::class, 'AddDocument'])->name('jobticket.AddDocument');

    });

    Route::get('/mindmap', [MindMapController::class, 'index'])->name('mindmap.index');
    Route::post('/mindmap', [MindMapController::class, 'mindmapstore'])->name('mindmap.store');
    Route::post('/mindmap-kind', [MindMapController::class, 'mindmapkindstore'])->name('mindmap-kind.store');

    Route::get('/file/data', [FileManagementController::class, 'data'])->name('file.data');
    Route::get('/file/download/{id}', [FileManagementController::class, 'downloadFile'])->name('file.download');
    Route::get('file/showFile/{id}', [FileManagementController::class, 'showFile'])->name('file.showFile');
    Route::resource('/file', FileManagementController::class);


    //backup technology
    Route::get('/run-backup', [BackupController::class, 'runBackup']);
    Route::get('/download-backup', [BackupController::class, 'downloadBackup']);



    Route::get('/edit-pdf', [PDFEditorController::class, 'form']);
    Route::post('/edit-pdf', [PDFEditorController::class, 'editPDF']);
    Route::get('/storage/{file}', function ($file) {
        return response()->download(storage_path('app/' . $file));

    });

    Route::get('/download-pdf', [PDFEditorController::class, 'editAndDownloadPDF']);



    Route::resource('wagroupnumbers', WagroupnumberController::class);
    Route::patch('wagroupnumbers/{wagroupnumber}/verify', [WagroupnumberController::class, 'verify'])->name('wagroupnumbers.verify');

    // jobticket update
    Route::get('/jobticket/closedJobTicket', [JobticketController::class, 'closedJobTicket']);
});


Route::get('/database-error', function () {
    return response()->view('errors.database', [], 503);
})->name('database.error');


//telegram
Route::get('telegram-messages-accounts/runtelegram', [TelegramMessagesAccountController::class, 'runtelegram'])->name('telegram_messages_accounts.runtelegram');
Route::get('telegram-messages-accounts/notifMemo', [NewMemoController::class, 'notifMemo']);
Route::get('notifMemowhatsapp', [NewMemoController::class, 'notifMemowhatsapp']);
Route::get('telegram-messages-accounts/notifMemoforhighrank', [NewMemoController::class, 'notifMemoforhighrank']);

//progressretrofit
Route::get('/newprogressreports/otomateprogressretrofit', [NewprogressreportController::class, 'otomateprogressretrofit'])->name('newprogressreports.otomateprogress');

// ppo dan fabrikasi
Route::get('/newprogressreports/whatsappsend', [NewprogressreportController::class, 'whatsappsend']);


Route::get('ujiwa', [TelegramMessagesAccountController::class, 'ujiwa']);

// backup tiap jam
Route::get('/backupsql', [OtomasiController::class, 'run_simpandatabaseinka']);
Route::get('/download-last-backup', [OtomasiController::class, 'download_last_backup']);


//register,login
Route::get('/register662400023', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register662400023', [AuthController::class, 'registerform'])->name('registerklik');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']);
Route::get('forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [AuthController::class, 'reset'])->name('password.update');

Route::get('numberverificator', [AuthController::class, 'numberverificator'])->name('auth.numberverificator');


// Allert Harian
Route::get('/document/allert', [HomeController::class, 'showAllmemoallert'])->name('allert.all');
Route::get('/document/allertunitall', [HomeController::class, 'memounitallert']);
Route::get('/cekhasil', [HomeController::class, 'progressall']);

//telegram
Route::get('/get-updates-telegramcommand', [OtomasiController::class, 'getUpdatestelegramcommand']);
Route::get('/backupsql', [OtomasiController::class, 'run_simpandatabaseinka']);

Route::get('/ujicobapdf', [FileController::class, 'ujicobapdf'])->name('documentMTPR.ujicobapdf');

// jobticket update
Route::get('/jobticket/updatedocumentsupport', [JobticketController::class, 'updateInfoHari']);
Route::get('/jobticket/show/{jobticket_identity_part}/{jobticket_identity_id}/{jobticket_id}/{jobticketstartedrev_id}/{position}', [JobticketController::class, 'approvebywa'])->name('jobticket.approvebywa');

// jadwal
Route::get('/getschedule', [EventController::class, 'getschedule']);
Route::get('/events/show/{id}', [EventController::class, 'show'])->name('events.show');
Route::post('/events/destroy/{id}', [EventController::class, 'destroy'])->name('events.destroy');
Route::post('/events/', [EventController::class, 'store'])->name('events.store');
Route::get('/download-daypilot-pdf', [EventController::class, 'downloadPDF'])->name('daypilot.index');



Route::get('/search-dokumen', [NewprogressreportController::class, 'searchdokumenbywa']);

Route::get('/streamdownloadfile', [FileController::class, 'streamdownloadfile'])->name('file.streamdownloadfile');


Route::get('/ujicoba', [NewprogressreportController::class, 'ujicoba']);

Route::get('/downloadganttchart/target/chart', [NewreportController::class, 'downloadChart']);
Route::get('/downloadganttchart/hasil/chart', [NewreportController::class, 'getProjectData']);

Route::get('/newreports/ganttchart/target/chart', [NewreportController::class, 'target'])->name('newreports.target');
Route::get('/newreports/areachart/jamorang/chart', [NewreportController::class, 'jamorang'])->name('newreports.jamorang');

Route::get('/ganttcharttenminutes/hasil/chart', [NewreportController::class, 'getProjectDatatenminutes'])->name('newreports.getProjectDatatenminutes');
Route::get('/ganttchart/hasil/chart', [NewreportController::class, 'getProjectData'])->name('newreports.getProjectData');



Route::get('/ganttchart/hasil/chart', [NewreportController::class, 'getProjectData'])->name('newreports.getProjectData');

Route::get('/areachart/hasil/chart', [NewreportController::class, 'getHoursProjectData'])->name('newreports.getHoursProjectData');



Route::get('/workloadproject', [NewprogressreportController::class, 'getproject'])->name('newreports.workloadproject');

