<section class="explore-section section-padding" id="section_2">
	
<div class="container">

	<h4>Tugas</h4>

	<!-- section search -->
	<div class="row mt-4">

		<div class="col-lg-4 col-md-12 col-sm-12">
			<div class="mb-3 row">
				<label for="select-mapel" class="col-sm-2 col-form-label">Mata Pelajaran</label>
				<div class="col-sm-10">
 
 
					<select class="form-select" name="select-mapel" id="select-mapel" aria-label="Pilih Matapelajaran">
						<option  value="" >==Pilih==</option>
						<?php foreach($mapelop as $key => $val) : ?>
							<option  value="<?=$val['subject_id']?>" ><?=$val['subject_name']?></option>
						<?php endforeach ?>
					</select>					
				</div>
			</div>
		</div>

		<div class="col-lg-4 col-md-12 col-sm-12">
			<div class="mb-3 row">
				<label for="start-date" class="col-lg-4 col-md-2 col-sm-2 col-form-label">Tanggal</label>
				<div class="col-lg-8 col-md-8 col-sm-8">
					<input type="date" class="form-control" id="start-date">
				</div>
			</div>
		</div>

		<div class="col-lg-4 col-md-12 col-sm-12">
			<div class="mb-3 row">
				<label for="end-date" class="col-lg-2 col-md-2 col-sm-2 col-form-label">s/d</label>

				<div class="col-lg-8 col-md-8 col-sm-8">
					<input type="date" class="form-control" id="end-date">
				</div>

				<div class="col-lg-2 col-md-2 col-sm-2 d-flex justify-content-end">
					<button class="btn btn-clear border shadow-sm" id="search">Cari</button>
				</div>
			</div>
		</div>

	</div>

	<?php if($this->session->userdata('user_level') == 3) : ?>
	<div class="row mt-4">
		<div class="container d-flex justify-content-end p-0">
			<a href="<?=base_url()?>task/create" class="btn btn-success">
				+ Buat Tugas
			</a>
		</div>
	</div>
	<?php endif ?>

	<!-- content -->
	<div class="row mt-4" id="task-content">

		
		
	</div>
		
	<nav aria-label="Page navigation example" class="d-flex justify-content-center">
		<ul class="pagination">
			
		</ul>
	</nav>
	
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
	var currentPage = 1;
	load_data(1,10);

	// KETIKA BUTTON CARI DI KLIK
	$('#search').on('click', function(e){
		load_data();
	});

	// create function load data
	function load_data(page = 1, limit = 10){
		let mapel = $('#select-mapel').val();
		let startDate = $('#start-date').val();
		let endDate = $('#end-date').val();

		$.ajax({
			type: "GET",
			url: BASE_URL+"task/getlist",
			data: {
				page: page,
				limit: limit,
				mapel: mapel,
				startDate: startDate,
				endDate: endDate
			},
			success: function (response) {
				$('#task-content').html('');
				$.each(response.task, function (key, value){
					let desc = value.note;

					if(desc.length > 100) desc = desc.substring(0, 300) + ' ...'

					$('#task-content').append(`
						<div class="container border rounded-4 bg-clear p-3 mb-3 task-item">
							<div class="d-flex justify-content-between">
								<a href="task/detail/${value.task_id}"><h6 class="mb-2">${value.subject_name}</h6></a>
								<p style="font-size: 14px;"><span class="task-card-date">batas akhir : ${value.due_date}</span></p>
							</div>
							<p style="font-size: 14px;"><a href="task/detail/${value.task_id}">${desc}</a></p>
							<div class="container d-flex justify-content-end">${buttonGroup(response.user_level, value.task_id)}</div>
						</div>
					`);
				});

				$('.pagination').html('');
				for(let i = 0; i < response.total_pages; i++){
					if(currentPage == i+1){
						$('.pagination').append(`
							<li class="page-item active"><a class="page-link" onclick="page(${i+1}, this)">${i+1}</a></li>
						`);
					}else{
						$('.pagination').append(`
							<li class="page-item"><a class="page-link" onclick="page(${i+1}, this)">${i+1}</a></li>
						`);
					}

				}
			}
		});
	}

	// JIKA PAGE NUMBER DI KLIK
	function page(pageNumber, e){
		currentPage = pageNumber;
		load_data(pageNumber);
	}

	// BUTTON GROUP EDIT & DELETE
	function buttonGroup(user_level, id){
		let buttonGroup = `<a href="${BASE_URL+'task/create/'+id}" class="btn btn-clear border d-inline me-1 rounded-5"><i class="bi bi-pencil-square"></i></a>
							<a class="btn btn-clear border d-inline rounded-5" onclick="deleteTask(${id})"><i class="bi bi-trash3-fill"></i></a>`;
		if(user_level == 3  ){
			return buttonGroup;
		}

		return '';
	}

	function deleteTask(id){
		Swal.fire({
			title: 'Are you sure?',
			text: "You won't be able to revert this!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!'
		}).then((result) => {
			if (result.isConfirmed) {

				$.ajax({
					type: "POST",
					url: BASE_URL+"task/delete",
					data: {
						id: id
					},
					dataType: "JSON",
					success: function (response) {
						if(response.success == true){
							Swal.fire('Deleted!', response.message, 'success');
							window.location.href = BASE_URL+'task';
						}
					}
				});

				
			}
		})
	}
</script>
