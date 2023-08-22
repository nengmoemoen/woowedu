'use strict';
const base_url  = document.querySelector('base').href,
        frm       = document.forms['form-add'],
        mapel     = document.getElementsByName('a_materi_subject')[0], 
        tanggal   = document.getElementsByName('a_materi_date')[0],
        s_title   = document.getElementsByName('s_title')[0] ;
    ;

(async $ => {

    /**
     *  DATATABLE
     * 
     */
    const table = $('#tbl_materi').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: base_url + 'api/materi/getAll'
        },
        select: {
			style:	'multi', 
			selector: 'td:first-child'
		},
        columns: [
            {
                data: null,
                className: 'select-checkbox',
                checkboxes: {
                    selectRow: true
                },
                orderable: false,
                render(data, type, row, _meta) {
                    return '';
                }
            },
            {
                data: 'materi_id',
                visible: false
            },
            {
                data: 'title'
            }, 
            {
                data: 'available_date',
               
            } ,
            {
                data:'subject_id',
                visible: false
            },
            {
                data: 'subject_name'
            },
            {
                data: null,
                render(data, row, type, meta) {
                    var view = '<div class="btn-group btn-group-sm float-right">'+
                                    '<button class="btn bg-purple text-white view_video"><i class="fas fa-desktop font-size-12"></i></button>' +
                                    '<button class="btn btn-success edit_subject"><i class="bx bx-edit-alt font-size-12"></i></button>' +
                                    '<button class="btn btn-sm btn-danger delete_subject"><i class="bx bx-trash font-size-12"></i></button>' +
                                '</div>';
                    return view;
                }
            }

        ]
    });

     //select_all
     $('#select_all').on('click', e => {
        if(e.target.checked)
            table.rows().select();
        else
            table.rows().deselect();
    });

    $('#btn-refresh').on('click', e => { 
        s_mapel.value = '';
        s_title.value = '';
        table.ajax.reload();
    });

      /**
     *  SELECT
     * 
     */
    await loadmapel();
    $(mapel).selectpicker('val', null); 

    document.querySelector('#reset-subject').addEventListener('click', e => { 
        $(mapel).selectpicker('val', '');
        document.querySelector('input[name="a_materi_subject_text"]').value = '';
    }); 

    $(mapel).on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        e.stopPropagation();
        e.preventDefault();
        const el = e.target;
        if(clickedIndex != null)
				{
            document.querySelector('input[name="a_materi_subject_text"]').value = (el.options[clickedIndex]).innerText;
						//alert((el.options[clickedIndex]).value);
						var _sid = (el.options[clickedIndex]).value;
						$.ajax({
							type : "POST",
							url : base_url + "materi/get_parent",
							data: {sid:_sid},
							beforeSend: function(){
								$('#materi_parent').html('<div >Loading..</div>');
							},
							success: function(msg){
								$('#materi_parent').html(msg);  							
							}
						});

						$.ajax({
							type : "POST",
							url : base_url + "materi/get_require",
							data: {sid:_sid},
							beforeSend: function(){
								$('#materi_require').html('<div >Loading..</div>');
							},
							success: function(msg){
								$('#materi_require').html(msg);  							
							}
						});
						
						
				}
    });
 
    // datepicker
    $(tanggal).datepicker({
        startDate: '-0d',
        autoclose: true
    });
      

    /**
     *  POST
     */
    let isUpdate = 0;

    // when btn add clicked
    document.getElementById('btn-add').addEventListener('click', e => {
        isUpdate = 0;
        frm.reset(); 
        $(mapel).selectpicker('val', null);
        // cek if any file binary within
        let fileInput = document.getElementById('videoFile');
        if(fileInput.files.length > 0)
        {
            let file = fileInput.files[0];
            URL.revokeObjectURL(file);
        }

        document.getElementById('preview').src = null;
        frm['a_id'].value = null;
    });

    $('#tbl_materi > tbody').on('click', '.btn.edit_subject', e => {
        isUpdate = 1;
        const count = table.row(e.target.parentNode.closest('tr')).count(),
              item = table.row(e.target.parentNode.closest('tr')).data();

        frm['a_id'].value = item.materi_id;
        frm['a_materi_title'].value = item.title; 
        $(mapel).selectpicker('val', item.subject_id); 
        frm['a_materi_subject_text'].value = item.subject_name;
        frm['a_materi_date'].value = item.available_date;
        frm['a_materi_note'].value = item.note;
        document.getElementById("preview").src = item.materi_file;


        $('#modal-add').modal('show');
        
    });

    // when btn edit is clicked
    // document.getElementById('btn-add').addEventListener('click', e => {
    //     isUpdate = 0;
    //     frm.reset();
    // });


    frm.addEventListener('submit', async e => await post(table, isUpdate));

    document.getElementById('save-subject').addEventListener('click', e => {
        let evt = new Event('submit');
        frm.dispatchEvent(evt);
        //table.ajax.reload();
    });

    /**
     *  preview before upload
     * 
     */
    document.getElementById('videoFile').addEventListener('change', e => {
        let el = e.target;

        let blob = el.files[0];
        let blobURL = URL.createObjectURL(blob);
        document.getElementById("preview").src = blobURL;
        document.getElementById("video-label").innerHTML = blob.name;
    });

    $('#tbl_materi > tbody').on('click', '.btn.view_video', e => {
        let target = e.target;
        const data = table.row($(target).parents('tr')).data();

        document.getElementById('judul').innerText = data.title;
        document.getElementById('video-file').src = data.materi_file;
        document.getElementById('note').innerHTML = null;
        document.getElementById('note').innerHTML = data.note;
        $('#modal-video').modal('show');
    });
    
       /**
     ===================================================
     *               DELETE DATA
     ===================================================
     */

     // DELETE ALL
     $('#delete_all').on('click', e => {
        var rows = table.rows({selected: true});
        var data = rows.data(),
            count = rows.count();
        var arr = [];
        if(count === 0) {
            Swal.fire({
				type: "error",
				title:'<h5 class="text-danger text-uppercase">Error</h5>',
				text: "No row selected !!!"
			});
			return false;
        }

        for(var i =0; i< count; i++) {
            arr.push(data[i].materi_id);
        }

        Swal.fire({
            title: "Anda Yakin ?",
            text: "Data yang dihapus tidak dapat dikembalikan",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn btn-success mt-2",
            cancelButtonColor: "#f46a6a",
            confirmButtonText: "Ya, Hapus Data",
            cancelButtonText: "Tidak, Batalkan Hapus",
            closeOnConfirm: false,
            closeOnCancel: false
        }).then(t => {
            if(t.value) {
                erase(arr, 1);
            }
        })
     });

    // DELETE ONE
    $('#tbl_materi tbody').on('click', '.btn.delete_subject', e => {
        var row = table.row($(e.target).parents('tr')).data();
        Swal.fire({
            title: "Anda Yakin ?",
            text: "Data yang dihapus tidak dapat dikembalikan",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn btn-success mt-2",
            cancelButtonColor: "#f46a6a",
            confirmButtonText: "Ya, Hapus Data",
            cancelButtonText: "Tidak, Batalkan Hapus",
            closeOnConfirm: false,
            closeOnCancel: false
            }).then(t => {
                if(t.value) {
                    erase(row.materi_id, 0);
                }
            })
        });

     function erase(data, isBulk) {
        return $.ajax({
            url: base_url + 'api/materi/delete',
            type: 'DELETE',
            data: JSON.stringify({data: data, isBulk: isBulk}),
            contentType: 'application/json',
            beforeSend(xhr, obj) {
                Swal.fire({
                    html: 	'<div class="d-flex flex-column align-items-center">'
                    + '<span class="spinner-border text-primary"></span>'
                    + '<h3 class="mt-2">Loading...</h3>'
                    + '<div>',
                    showConfirmButton: false,
                    width: '10rem'
                });
            },
            success(resp) {
                Swal.fire({
					type: resp.err_status,
					title:`<h5 class="text-${resp.err_status} text-uppercase">${resp.err_status}</h5>`,
					html: resp.message
				});
				//csrfToken.content = resp.token;
            },
            error(err) {
                let response = JSON.parse(err.responseText);
				Swal.fire({
					type: response.err_status,
					title: '<h5 class="text-danger text-uppercase">'+response.err_status+'</h5>',
					html: response.message
				});
				//if(response.hasOwnProperty('token'))
				//	csrfToken.setAttribute('content', response.token);
            },
            complete() {
                table.ajax.reload();
            }
        });
     }
    


    /**
     *  for filter datatable
     * 
     */

     /**
	   * 
	   * 		SEARCH
	   * 
	   */
	  
        $('#search-button').on('click', e => {
            if(s_title.value !== '' || s_title.value !== null)
                table.columns(2).search(s_title.value).draw(); 
            if(s_mapel.value !== null || s_mapel.value !== '')
                table.columns(5).search(s_mapel.value).draw();
        });
        $('#reset-search').on('click', e => {
            if(s_title.value !== '' || s_title.value !== null) {
                s_title.value = null;
                table.columns(2).search(s_title.value).draw();
            } 
            if(s_mapel.value !== null || s_mapel.value !== '') {
                s_mapel.value = null;
                table.columns(5).search(s_mapel.value).draw();
            }
        });

        /**
         * 

         * IMPORT DATA
        */

			 let impForm = document.forms['import-area'];
			 let impFormat = impForm['import-format'],
				 impFile = impForm['file-upload'];
		 
			 let importFormat = null,
				 importFile = null;
			 /*for(let i of impFormat) {
				 i.addEventListener('click', e => {
					 importFormat = i.value;
				 });
				 // TODO
				 // 1. Buat input file accept mime type by file fformat radio btn
			 }
			 console.log(importFormat);
			 */
			 impFile.addEventListener('change', e => {
				 e.preventDefault();
				 let lbl = document.querySelector('.custom-file-label');
				 lbl.innerHTML = e.target.files[0].name;
				 importFile = e.target.files[0];
			 });
			 impForm.addEventListener('submit', e => {
				 e.preventDefault();
				 let fdata = new FormData();
				 fdata.append('format', impFormat.value);
				 fdata.append('upload-file', importFile);
				 // upload 
				 $.ajax({
					 xhr: function() {
						var xhr = new window.XMLHttpRequest();
                        var prog = document.getElementById('import-progress-1');
						xhr.upload.addEventListener('progress', e1 => {
							if(e1.lengthComputable) {
                                prog.removeAttribute('hidden');
								var completed = (e1.loaded === e1.total) ? 90 : Math.round((e1.loaded / e1.total) * 100);
								prog.getElementsByClassName('progress-bar')[0].setAttribute('aria-valuenow', completed);
								prog.getElementsByClassName('progress-bar')[0].style.width = completed + '%';
                                prog.getElementsByClassName('progress-bar')[0].innerHTML = completed + '%';
							}
						}, false);
						xhr.addEventListener('progress', e2 => {
							if(e2.lengthComputable) {
                                prog.removeAttribute('hidden');
								var completed = (e2.loaded === e2.total) ? 90 : Math.round((e2.loaded / e2.total) * 100);
								prog.getElementsByClassName('progress-bar')[0].setAttribute('aria-valuenow', completed);
                                prog.getElementsByClassName('progress-bar')[0].style.width = completed + '%';
                                prog.getElementsByClassName('progress-bar')[0].innerHTML = completed + '%';
							}
						}, false);

						return xhr;
					 },
					 url: base_url + 'api/materi/import',
					 type: 'POST',
					 data: fdata,
					 contentType: false,
					 processData: false,
					 success(reslv) {
						 //console.log(reslv);
                        var prog = document.getElementById('import-progress-1');

                        prog.getElementsByClassName('progress-bar')[0].setAttribute('aria-valuenow', 100);
                        prog.getElementsByClassName('progress-bar')[0].style.width = '100%';
                        prog.getElementsByClassName('progress-bar')[0].innerHTML = '100%';

						var res = reslv;
					    Swal.fire({
							type: res.err_status,
							title: '<h5 class="text-success text-uppercase">'+res.err_status+'</h5>',
							html: res.message
					    });
					 },
					 error(err) {
						 let response = JSON.parse(err.responseText);

						 Swal.fire({
							 type: response.err_status,
							 title: '<h5 class="text-danger text-uppercase">'+response.err_status+'</h5>',
							 html: response.message
						 });
					 },
					 complete() {
						 table.ajax.reload();
                         
					 }
				 });
			});
    
    $('#modal-import').on('hidden.bs.modal', function(e) {
        impForm.reset();
        impForm.querySelector('.custom-file-label').innerHTML = 'Pilih File';
        var progress = document.getElementById('import-progress');
        if(progress.hasAttribute('style')) {
            progress.getElementsByClassName('progress-bar')[0].removeAttribute('style');
            progress.getElementsByClassName('progress-bar')[0].setAttribute('aria-valuenow', 0);
            progress.getElementsByClassName('progress-bar')[0].innerHTML = '';
        }

        if(!progress.hasAttribute('hidden'))
            progress.hidden = true;
        
    });

})(jQuery);

/**
 * load subject data for select option
 * 
 * @return void
 */
async function loadmapel() {
    let subject  = await fetch(`${base_url}api/subject/getAll`);
    let getDatas = await subject.json();
    mapel.innerHTML = null;
    for(let item of getDatas.data) 
    {
        let opt = document.createElement('option');
        opt.value = item.id_mapel;
        opt.text  = item.nama_mapel;
        mapel.add(opt);
    }
    $(mapel).selectpicker();
}

 
/**
 * POST and upload video to server
 * 
 * @return void
 */



async function post(table, isUpdate) {
    try {

        const fd = new FormData(frm); 

        let url = isUpdate == 0 ? `${base_url}api/materi/save` : `${base_url}api/materi/edit`;
        const progressBar = document.getElementById('upload-progress');


        $.ajax({
            url: url,
            type:'POST',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            xhr: () => {
                var xhr = new window.XMLHttpRequest();

                // Upload progress
                xhr.upload.addEventListener("progress", function(evt){
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;

                        progressBar.classList.remove('d-none');
                        (progressBar.getElementsByClassName('progress-bar')[0]).style.width = (percentComplete * 100) + '%';
                        (progressBar.getElementsByClassName('progress-bar')[0]).setAttribute('aria-valuenow', percentComplete * 100);
                    }
               }, false); 
               return xhr;
            },
            beforeSend(xhr, obj) { 
            },
            async success(resp) {
                
                const f = await fetch(`${base_url}api/materi/importProgress`);
                console.log(f.json());

                
                Swal.fire({
					type: resp.err_status,
					title:`<h5 class="text-${resp.err_status} text-uppercase">${resp.err_status}</h5>`,
					html: `<h5 class="text-success">${resp.message} </h5>`,
                    timer: 1500
				})
                .then(t => $('#modal-add').modal('hide'));
				//csrfToken.content = resp.token;
            },
            error(err) {
                let response = JSON.parse(err.responseText);

                if(err.response)
                    response = JSON.parse(err.responseText);

								Swal.fire({
									type: response.err_status,
									title: '<h5 class="text-danger text-uppercase">'+response.err_status+'</h5>',
									html: `<h5 class="text-danger">${response.message} </h5>`
								}); 
            },
            complete() {
                progressBar.classList.add('d-none');
                table.ajax.reload
            }
         });
    }
    catch(err) {
        
    }
}