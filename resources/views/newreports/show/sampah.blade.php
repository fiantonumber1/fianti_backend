<div style="display: flex; justify-content: space-between; align-items: center;">
                    <label for="tambah-level" style="flex: 1;">Level</label>
                    <select id="tambah-level" class="swal2-input" style="flex: 2;">
                        <option value="-">-</option>
                        <option value="Predesign">Predesign</option>
                        <option value="Intermediate Design">Intermediate Design</option>
                        <option value="Final Design">Final Design</option>
                    </select>
                </div>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <label for="tambah-drafter">Drafter</label>
                    <select id="tambah-drafter" class="swal2-input">
                        ${drafterOptions}
                    </select>
                </div>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <label for="tambah-checker">Checker</label>
                    <select id="tambah-checker" class="swal2-input">
                        ${drafterOptions}
                    </select>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <label for="tambah-deadlinerelease" style="flex: 1;">Deadline Release</label>
                    <input id="tambah-deadlinerelease" class="swal2-input" placeholder="Deadline Release" style="flex: 2;">
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <label for="tambah-realisasi" style="flex: 1;">Realisasi</label>
                    <input id="tambah-realisasi" class="swal2-input" placeholder="Realisasi" style="flex: 2;">
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <label for="tambah-status" style="flex: 1;">Status Dokumen</label>
                    <select id="tambah-status" class="swal2-input" style="flex: 2;">
                        <option value="RELEASED">RELEASED</option>
                        <option value="Working Progress">Working Progress</option>
                        <option value="-">-</option>
                    </select>
                </div>