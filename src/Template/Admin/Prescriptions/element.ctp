    <?php

    echo'<div class="clearfix">
        <div class="col-sm-6">
            <div class="form-row">
                <label class="name">Patients<span class="required" aria-required="true">*</span></label>
                <div class="inputs">';
                    echo $this->Form->input('user_id', ['options' => $users, 'empty' => 'Select', 'class'=>'form-control selectpicker', 'data-live-search'=>true, 'label'=>false, 'required'=> true ]);
                echo '</div>
            </div>
        </div>';

        echo'<div class="col-sm-6">
            <div class="form-row">
                <label class="name">Diagnosis<span class="required" aria-required="true"></span></label>
                <div class="inputs">';
                    echo $this->Form->input('diagnosis', ['class' => 'form-control', 'label' => false, 'required' => true, 'type' =>'text']);
                echo '</div>
            </div>
        </div>
    </div>';

    echo '<div class="medicines_wrap" id="medicinesWrap">
        <button type="button" id="addMoreBtn" class="add_more_btn"><span class="fa fa-plus"></span></button>';
        $html = '<div class="medicines_row clearfix" id="medicinesRow">';
            $html .= '<div class="col-sm-5">';
                $html .= '<div class="form-row">';
                    $html .= '<label class="name">Medicines<span class="required" aria-required="true"></span></label>';
                        $html .= '<div class="inputs">';
                            $html .=  $this->Form->input('medicines.medicine_id[]', ['options' => $medicines, 'empty' => 'Select', 'class'=>'form-control selectpicker', 'data-live-search'=>true, 'label'=>false]);
                    $html .= '</div>';
                $html .= '</div>';
            $html .= '</div>';

            $html .= '<div class="col-sm-5">';
                $html .= '<div class="form-row">';
                    $html .= '<label class="name">Rules<span class="required" aria-required="true"></span></label>';
                        $html .= '<div class="inputs">';
                            $html .=  $this->Form->input('medicines.rule[]', ['class'=>'form-control', 'placeholder'=>'0-1-0', 'label'=>false]);
                        $html .=  '</div>';
                $html .= '</div>';
            $html .= '</div>';

            $html .=  '<div class="col-sm-2">';
                $html .= '<div class="inputs">';
                    $html .= '<button type="button" class="dle_medicine_btn"><span class="fa fa-minus"></span></button>';
                $html .= '</div>';
            $html .= '</div>';
        $html .= '</div>';
        echo  $html;
    echo '</div>

    <div class="col-sm-6">
        <div class="form-row">
            <label class="name">Tests<span class="required" aria-required="true"></span></label>
            <div class="inputs">';
                echo $this->Form->input('tests._ids', ['options' => $tests,'class'=>'form-control','label'=>false]);
            echo '</div>
        </div>
    </div>
    ';

    ?>





<script type="text/javascript">
    $(document).ready(function(){
        // Add field
        $("#addMoreBtn").click(function(){
            $(".dle_medicine_btn").css('display');
            $("#medicinesWrap").append('<?php echo $html ?>');
            $(".selectpicker").selectpicker('refresh');
        });

        // Delete field
        $(".medicines_wrap").on('click','.dle_medicine_btn',function(){
            $(this).parents('#medicinesRow').remove();
        });

    });

</script>



