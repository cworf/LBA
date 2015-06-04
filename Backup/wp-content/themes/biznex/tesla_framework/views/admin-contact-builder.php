<div class="form-builder-holder">
	<header class="fb-header">
		<h2> <i class="icon-form-builder"></i> Tesla Form Builder</h2>
	</header>
	<div class='main-container'>
		<div id="contact-builder-wrapper">
			<div class="row-fluid">
				<div class="span8 builder">
					<button class='btn btn-success save-forms'>Save forms</button>
					<?php 
					//print_r($forms);
					if(!empty($forms)) : ;
						foreach ($forms as $form) : ?>	<!-- Start Form template -->
							<div class="builder-body form-template">
								<header class="form-meta">
									<input type="text" class="form-id" placeholder="Unique Form ID" value="<?php echo $form['id'] ?>">
									<input type="text" class="form-receiver-email" placeholder="Receiver's email (empty for admin email)" value="<?php echo $form['receiver_email'] ?>">
									<select name="form_location" class="form_location">
										<option value="">No Location</option>
										<?php foreach ($locations as $id => $name): ?>
											<option value="<?php echo $id?>"<?php selected( $form['location'], $id );?>><?php echo $name ?></option>
										<?php endforeach ?>
									</select>
								</header>
								<?php if(!empty($form['rows']))
								foreach($form['rows'] as $row) : ?> <!-- Start Row -->
									<div class="row-fluid form-row">
										<?php foreach( $row['columns'] as $column ) : ?> <!-- Start Column -->
											<div class="span<?php echo $column['size']?> column">
												<ul>
													<?php if (!empty($column['form_elements']))
														foreach($column['form_elements'] as $form_element) : ?>
															<li 
																class="form-element" 
																data-element='<?php echo json_encode($form_element);//building element parameters json in the data attr ?>'
																><?php echo $form_element['title'] ?>
																	<span class='config'><i class='icon-pencil'></i></span>
															</li>
														<?php endforeach; ?>
												</ul>
											</div>
										<?php endforeach;?><!-- End Column -->
										
										<span class="row-edit">+</span>
										<span class="row-delete">&times;</span>
										<ul class="column-picker clearfix">
											<li data-columns='6,6'>6-6</li>
											<li data-columns='8,4'>8-4</li>
											<li data-columns='4,4,4'>4-4-4</li>
											<li data-columns='4,8'>4-8</li>
										</ul>
									</div>
								<?php endforeach;?><!-- End Row -->
								<button class='add-new-row btn btn-info'>Add row</button>
								<span class="form-delete">&times;</span>
							</div>
						<?php endforeach;?><!-- End Form Template -->
					<?php else : ?>
						<div class="builder-body form-template">
							<header class="form-meta">
								<input type="text" class="form-id" placeholder="Unique Form ID">
								<input type="text" class="form-receiver-email" placeholder="Receiver's email (empty for admin email)" value="">
								<select name="form_location" class="form_location">
									<option value="">No Location</option>
									<?php foreach ($locations as $id => $name): ?>
										<option value="<?php echo $id?>"><?php echo $name ?></option>
									<?php endforeach ?>
								</select>
							</header>
							<div class="row-fluid form-row">
								<div class="span12 column">
									<ul></ul>
								</div>
								<span class="row-edit">+</span>
								<span class="row-delete">&times;</span>
								<ul class="column-picker clearfix">
									<li data-columns='6,6'>6-6</li>
									<li data-columns='8,4'>8-4</li>
									<li data-columns='4,4,4'>4-4-4</li>
									<li data-columns='4,8'>4-8</li>
								</ul>
							</div>
							<button class='add-new-row btn btn-info'>Add row</button>
						</div>
					<?php endif; ?>
				<button id="add-new-form" class='btn btn-primary'>Add form</button>
				<button class='btn btn-success save-forms' data-loading-text="Saving..." data-success-text="Successfully Saved" data-fail-text="Couldn't save or nothing changed !">Save forms</button>
			</div>
				<aside class="span4 tools">
					<ul class="form-elements" data-spy="affix">
						<?php foreach ($form_elements as $key => $form_element) : ?>
							<li class='form-element' data-element='{<?php echo (empty($form_element['no_name'])) ? '"name":"",' : ''?>"type":"<?php echo $key ?>","title":"<?php echo $form_element['title'] ?>"<?php 
							if(!empty($form_element['properties'])){
								echo ",";
								$nr_prop = 1;
								foreach ($form_element['properties'] as $prop_name => $property) {
									echo '"'.$prop_name.'":"'.$property.'"';
									echo $nr_prop < count($form_element['properties']) ? "," : "";
									$nr_prop++;
								}
							}?>}'><?php echo $form_element['title'] ?>
						<?php if(!empty($form_element['properties']) || empty($form_element['no_name'])) : ?>
							<span class='config'><i class='icon-pencil'></i></span>
						<?php endif; ?>
						</li>
						<?php endforeach; ?>
						<!-- <li class='form-element' data-element='{"type":"textarea","title":"Textarea","placeholder":"","label":""}'>Textarea<span class='config'><i class='icon-pencil'></i></span></li>
						<li class='form-element' data-element='{"type":"email","title":"Email","placeholder":"","label":""}'>Email<span class='config'><i class='icon-pencil'></i></span></li>
						<li class='form-element' data-element='{"type":"select","title":"Select","select_options":"","label":""}'>Select<span class='config'><i class='icon-pencil'></i></span></li>
						<li class='form-element' data-element='{"type":"checkbox","title":"Checkbox","label":""}'>Checkbox<span class='config'><i class='icon-pencil'></i></span></li>
						<li class='form-element' data-element='{"type":"result","title":"Result","placeholder":"","label":""}'>Result<span class='config'><i class='icon-pencil'></i></span></li>
						<li class='form-element' data-element='{"type":"submit","title":"Submit"}'>Submit<span class='config'><i class='icon-pencil'></i></span></li> -->
					</ul>
				</aside>
			</div>
		</div>
	</div>

	<div id="edit-form-element-modal" class="modal hide">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	    <h3>Edit form element</h3>
	  </div>
	  <div class="modal-body">
	    <form action="edit-form-element" method='post'>
	    	<label class="element-property"><b>Name</b> <input class='name' name="name" type="text" value=''></label>
	    	<label class="element-property"><b>Label</b> <input class='element-label' name="label" type="text" value=''></label>
	    	<label class="element-property add-on"><b>Placeholder</b> <input class='placeholder' name="placeholder" type="text" value=''></label>
	    	<label class="element-property add-on"><b>Required</b> <input class='required' name="required" type="checkbox" value='data-required="true"'></label>
	    	<label class="element-property add-on"><b>Select Options</b> <input class='select-options' name="select_options" type="text" value='' placeholder="Option1,Option2,Option3"></label>
	    </form>
	  </div>
	  <div class="modal-footer">
	    <a href="#" class="submit btn btn-primary">Save changes</a>
	  </div>
	</div>
</div> <!-- /.form-builder-holder -->