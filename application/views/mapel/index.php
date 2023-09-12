<section class="explore-section section-padding" id="section_2">
	
<div class="container">

	<div class="row ">
		<div class="col-lg-6 col-md-6 col-sm-12 mb-2">
			<select class="form-select" name="select-class" aria-label="Pilih Kelas">
				<option  value="1" selected>Kelas 1</option>
				<option value="2">Kelas 2</option>
				<option value="3">Kelas 3</option>
			</select>
		</div>

		
		<div class="col-lg-6 col-md-6 col-sm-12 mb-5">
			<div class="input-group mb-3">
				<select class="form-select" name="select-mapel" id="basic-usage" aria-label="Pilih Matapelajaran">
					<option  value="1" selected>Kelas VI Matematika (Kumer)</option>
					<option  value="2" selected>Kelas VI Bahasa Indonesia (Kumer)</option>
					<option  value="3" selected>Kelas VI IPA (Kumer)</option>
					<option  value="4" selected>Kelas VI IPS (Kumer)</option>
					<option  value="5" selected>Kelas VI Bahasa Inggris (Kumer)</option>
					<option  value="5" selected>Kelas VI Bahasa Inggris (Kumer)</option>
				</select>
				
				<button class="btn btn-outline-secondary" type="button" id="button-addon2"><i class="bi bi-search"></i></button>
			</div>
		</div>
	</div>

	<div class="row">
		<!-- <div class="col"> -->
			<!--<?php for($i=0; $i<10; $i++):?>
			<div class="col-lg-4 col-md-6">
				<div class="card rounded border mb-4">
					<div class="row">
						<div class="col-lg-5 col-md-5 col-sm-3 col-xs-3">
							<div class="container mt-2">
								<img src="<?=base_url()?>assets/images/faq_graphic.jpg" alt="" width="90" height="125">
							</div>
						</div>
						<div class="col-lg-7 col-md-5 col-sm-9 col-xs-9">
							<p class="title">Buku Interaktif: PR Bahasa Indonesia X Semester 1 2022 </p>
							<p class="font-weight-bold fs-14 p-3">Kelas 10 Bahasa Indonesia (Interaktif)</p>
						</div>
					</div>
				</div>
			</div>
			<?php endfor?> -->
		<!-- </div> -->
		<div class="row mb-2">
			<div class="col-md-8 col-lg-10"></div>
			<div class="col-md-4 col-lg-2 d-flex flex-nowrap justify-content-end">
				<button type="button" class="btn btn-sm btn-success text-white shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modal-add">
					<i class="bi bi-plus font-size-12"></i> Tambah
				</button>
			</div>
		</div>

		<div class="row"></div>
		<?php 
			if($datamodel != 'grid'):
				$this->load->view('mapel/table_view');
			endif; 
		?>
	</div>


</div>

	
</section>

<!-- Modal add -->
<section class="modal fade" tabindex="-1" id="modal-add">
  <div class="modal-dialog modal-xl">
    <div class="modal-content border-0">
      <div class="modal-header bg-success">
        <h5 class="modal-title text-capitalize text-light text-shadow">Tambah Materi</h5>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

      </div>
      <div class="modal-body">
        <form name="form-add" id="form-add" class="d-flex flex-column"  >
            <div class="row">
                <div class="col-12 col-lg-7">
                     
                    <div class="row align-items-top mb-3">
                        <div class="col-3">
                            <label class="m-0">Tema <span class="text-danger"><strong>*</strong></span></label>
                        </div>
                        <div class="col-8 mb-3">
													  <input type="text" class="form-control form-control-sm" name="a_materi_tema_title" />
                        </div>			


                        <div class="col-3">
                            <label class="m-0">Sub Tema <span class="text-danger"><strong>*</strong></span></label>
                        </div>
                        <div class="col-8 mb-3">
													  <input type="text" class="form-control form-control-sm" name="a_materi_sub_tema_title" />
                        </div>											
                        <div class="col-3">
                            <label class="m-0">Judul <span class="text-danger"><strong>*</strong></span></label>
                        </div>
                        <div class="col-8 mb-3">
                            <input type="text" class="form-control form-control-sm" name="a_materi_title" />
                        </div>
                        <div class="col-3">
                            <label class="m-0">No Urut <span class="text-danger"><strong>*</strong></span></label>
                        </div>
                        <div class="col-8 mb-3">
                            <input type="number" class="form-control form-control-sm" name="a_materi_no_urut" />
                        </div>												
                        <div class="col-3">
                            <label class="m-0">Mata Pelajaran <span class="text-danger"><strong>*</strong></span></label>
                        </div>
                        <div class="col-8 mb-3">
                            <select class="form-select form-select-sm col-11" name="a_materi_subject" data-live-search="true"></select>
                            <button type="button" id="reset-subject" class="btn btn-sm btn-primary"><i class="fas fa-undo"></i></button>
                            <input type="hidden" name="a_materi_subject_text">
                        </div> 

	
												
												<!--
                        <div class="col-3">
                            <label class="m-0">Tanggal Di Buka <span class="text-danger"><strong>*</strong></span></label>
                        </div>
                        <div class="col-8 mb-3">
                            <input type="text" class="form-control form-control-sm" name="a_materi_date" />
                        </div>-->
												
                        <div class="col-3">
                            <label>Deskripsi </label>
                        </div>
                        <div class="col-8 mb-3">
                            <textarea class="form-control form-control-sm w-100 h-100" rows="12" name="a_materi_note"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <h4 class="mb-4 text-underline">UPLOAD VIDEO</h4>
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex flex-column">
                                <video id="preview" class="w-100" height="265" controls></video>
                                <h6 class="mt-1">Preview</h6>
                                <div class="custom-file mt-2">
                                    <input type="file" class="custom-file-input" id="videoFile" name="a_materi_video">
                                    <label class="custom-file-label overflow-hidden" id="video-label" for="videoFile" data-browse="Unggah Video">Pilih Video</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="a_id" />
            <input type="hidden" name="xsrf" />
        </form>
        <span class="w-100 d-flex flex-nogrow">
          <!-- PRogress bar-->
            <div id="upload-progress" class="progress w-100 d-none">
              <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          <!-- end PRogress bar-->
        </span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="save-subject">Simpan</button>
      </div>
    </div>
  </div>
</section>

<!-- end modal add-->

<script>
	// $('#basic-usage').select2({
	// 	theme: "bootstrap-5",
	// 	width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
	// 	placeholder: $( this ).data( 'placeholder' ),
	// });
</script>
