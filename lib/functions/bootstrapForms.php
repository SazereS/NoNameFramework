<?php

// Bootstrap form constructor

function bootstrapFormDecorators($form_type){
    $decorators = array(
        'form-horizontal' => array(
            'text' => <<<DECORATOR
<div class="control-group%s">
    <label class="control-label" for="input-%s">%s</label>
    <div class="controls">
        %s
        %s
    </div>
</div>
DECORATOR
            ,
            'checkbox' => <<<DECORATOR
<div class="control-group%s">
    <div class="controls">
        <label class="checkbox">
            %s %s
            %s
        </label>
    </div>
</div>
DECORATOR
            ,
            'radio' => <<<DECORATOR
<div class="control-group%s">
    <label class="control-label">%s</label>
    <div class="controls">
        %s
        %s
    </div>
</div>
DECORATOR
            ,
            'button' => <<<DECORATOR
<div class="control-group">
    <div class="controls">
        %s
        %s
    </div>
</div>
DECORATOR
        ),
    );
    $decorators['form-vertical'] = $decorators['form-horizontal'];
    return $decorators[$form_type];
}

function bootstrapFormCreateInput($type, $name, Array $params = array(), Array $errors = array(), $form_type = 'form-horizontal'){
    $decorators = bootstrapFormDecorators($form_type);
    switch($type){
        # LINK
        case 'link':
            $input_params = array(
                'id' => 'input-' . $name,
                'class' => array('btn')
            );
            $input_params = array_merge($params, $input_params);
            $label = $input_params['label'];
            unset($input_params['label']);
            if($params['class']) $input_params['class'] = array_merge($params['class'], $input_params['class']);
            if($input_params['class']) $input_params['class'] = implode(' ', $input_params['class']);
            foreach($input_params as $param => $value){
                $params_strings[] = $param . '="' . $value . '"';
            }
            $input = sprintf('<a %s>%s</a>', implode(' ', $params_strings), $label);
            $result = sprintf($decorators['button'], $input, '');
        break;

        # BUTTON, SUBMIT AND RESET
        case 'button':
        case 'submit':
        case 'reset':
            $input_params = array(
                'type' => $type,
                'name' => $name,
                'id' => 'input-' . $name,
                'class' => array('btn')
            );
            if($params['btn']){
                $input_params['class'][] = 'btn-' . $params['btn'];
            } else {
                if($type == 'submit'){
                    $input_params['class'][] = 'btn-primary';
                }
                if($type == 'reset'){
                    $input_params['class'][] = 'btn-danger';
                }
            }
            $input_params = array_merge($params, $input_params);
            if(!empty($errors)){
                $errors_string = '<ul><li class="text-error">' . implode('</li><li class="text-error">', $errors) . '</li></ul>';
            }
            $label = $input_params['label'];
            unset($input_params['label']);
            if($params['class']) $input_params['class'] = array_merge($params['class'], $input_params['class']);
            if($input_params['class']) $input_params['class'] = implode(' ', $input_params['class']);
            foreach($input_params as $param => $value){
                $params_strings[] = $param . '="' . $value . '"';
            }
            $input = sprintf('<button %s>%s</button>', implode(' ', $params_strings), $label);
            $result = sprintf($decorators['button'], $input, $errors_string);
        break;

        # SELECT
        case 'select':
            $input_params = array(
                'type' => $type,
                'name' => $name,
                'id' => 'input-' . $name,
            );
            $input_params = array_merge($params, $input_params);
            if(!empty($errors)){
                $errors_string = '<ul><li class="text-error">' . implode('</li><li class="text-error">', $errors) . '</li></ul>';
                $error_class = ' error';
            }
            if($input_params['label']){
                $label = $input_params['label'];
                unset($input_params['label']);
            }
            if($params['class']) $input_params['class'] = array_merge($params['class'], $input_params['class']);
            if($input_params['class']){
                $input_params['class'] = implode(' ', $input_params['class']);
            }
            if($input_params['value']){
                $rvalue = $input_params['value'];
                unset($input_params['value']);
            }
            if($input_params['values']){
                $values = $input_params['values'];
                unset($input_params['values']);
            } else {
                return false;
            }
            foreach($input_params as $param => $value){
                $params_strings[] = $param . '="' . $value . '"';
            }
            foreach($values as $rdv => $rdl){
                $input .= sprintf(
                        '<option %s>%s</option>',
                        implode(
                                ' ',
                                array_merge(
                                        array('value="' . $rdv . '"'),
                                        ($rdv == $rvalue) ? array('selected="selected"') : array()
                                )
                        ),
                        $rdl
                );
            }
            $input = sprintf('<select %s >%s</select>', implode(' ', $params_strings), $input);
            $result = sprintf($decorators['text'], $error_class, $name, $label, $input, $errors_string);
        break;

        # RADIO
        case 'radio':
            $input_params = array(
                'type' => $type,
                'name' => $name,
                'id' => 'input-' . $name,
            );
            $input_params = array_merge($params, $input_params);
            if(!empty($errors)){
                $errors_string = '<ul><li class="text-error">' . implode('</li><li class="text-error">', $errors) . '</li></ul>';
                $error_class = ' error';
            }
            if($input_params['label']){
                $label = $input_params['label'];
                unset($input_params['label']);
            }
            if($params['class']) $input_params['class'] = array_merge($params['class'], $input_params['class']);
            if($input_params['class']){
                $input_params['class'] = implode(' ', $input_params['class']);
            }
            if($input_params['value']){
                $rvalue = $input_params['value'];
                unset($input_params['value']);
            }
            if($input_params['values']){
                $values = $input_params['values'];
                unset($input_params['values']);
            } else {
                return false;
            }
            foreach($input_params as $param => $value){
                $params_strings[] = $param . '="' . $value . '"';
            }
            foreach($values as $rdv => $rdl){
                $input .= sprintf(
                        '<label class="radio"><input %s /> %s</label>',
                        implode(
                                ' ',
                                array_merge(
                                        $params_strings,
                                        array('value="' . $rdv . '"'),
                                        ($rdv == $rvalue) ? array('checked="checked"') : array()
                                )
                        ),
                        $rdl
                );
            }
            $result = sprintf($decorators['radio'], $error_class, $label, $input, $errors_string);
        break;

        # CHECKBOX
        case 'checkbox':
            $input_params = array(
                'type' => $type,
                'name' => $name,
                'id' => 'input-' . $name,
            );
            $input_params = array_merge($params, $input_params);
            if(!empty($errors)){
                $errors_string = '<ul><li class="text-error">' . implode('</li><li class="text-error">', $errors) . '</li></ul>';
                $error_class = ' error';
            }
            if($params['class']) $input_params['class'] = array_merge($params['class'], $input_params['class']);
            if($input_params['class']) $input_params['class'] = implode(' ', $input_params['class']);
            foreach($input_params as $param => $value){
                $params_strings[] = $param . '="' . $value . '"';
            }
            $input = sprintf('<input %s />', implode(' ', $params_strings));
            $result = sprintf($decorators['checkbox'], $error_class, $input, $params['label'], $errors_string);
        break;

        # TEXTAREA
        case 'textarea':
            $input_params = array(
                'name' => $name,
                'id' => 'input-' . $name,
                'placeholder' => $params['label']
            );
            $input_params = array_merge($params, $input_params);
            if(!empty($errors)){
                $errors_string = '<ul><li class="text-error">' . implode('</li><li class="text-error">', $errors) . '</li></ul>';
                $error_class = ' error';
            }
            if($params['class']) $input_params['class'] = array_merge($params['class'], $input_params['class']);
            if($input_params['class']) $input_params['class'] = implode(' ', $input_params['class']);
            foreach($input_params as $param => $value){
                $params_strings[] = $param . '="' . $value . '"';
            }
            if($input_params['value']){
                $text = $input_params['value'];
                unset($input_params['value']);
            }
            $input = sprintf('<textarea %s>%s</textarea>', implode(' ', $params_strings), $text);
            $result = sprintf($decorators['text'], $error_class, $name, $params['label'], $input, $errors_string);
        break;

        # HIDDEN
        case 'hidden':
            $input_params = array(
                'type' => $type,
                'name' => $name,
                'id' => 'input-' . $name,
            );
            $input_params = array_merge($params, $input_params);
            if($params['class']) $input_params['class'] = array_merge($params['class'], $input_params['class']);
            if($input_params['class']) $input_params['class'] = implode(' ', $input_params['class']);
            foreach($input_params as $param => $value){
                $params_strings[] = $param . '="' . $value . '"';
            }
            $result = sprintf('<input %s />', implode(' ', $params_strings));
        break;

        # TEXT, EMAIL AND PASSWORD
        case 'text':
        case 'email':
        case 'password':
        default:
            $input_params = array(
                'type' => $type,
                'name' => $name,
                'id' => 'input-' . $name,
                'placeholder' => $params['label']
            );
            $input_params = array_merge($params, $input_params);
            if(!empty($errors)){
                $errors_string = '<ul><li class="text-error">' . implode('</li><li class="text-error">', $errors) . '</li></ul>';
                $error_class = ' error';
            }
            if($params['class']) $input_params['class'] = array_merge($params['class'], $input_params['class']);
            if($input_params['class']) $input_params['class'] = implode(' ', $input_params['class']);
            foreach($input_params as $param => $value){
                $params_strings[] = $param . '="' . $value . '"';
            }
            $input = sprintf('<input %s />', implode(' ', $params_strings));
            $result = sprintf($decorators['text'], $error_class, $name, $params['label'], $input, $errors_string);
        break;
    }
    return $result;
}

function bootstrapCreateForm(Array $params = array(), Array $elements = array(), Array $values = array(), Array $errors = array(), $form_type = 'form-horizontal'){
    $decorator = <<<DECORATOR
<form %s>
    %s
</form>
DECORATOR;
    switch($form_type){
        default:
            $form_type = 'form-horizontal';
        case 'form-horizontal':
        case 'form-vertical':
            $d_params = array(
                'class' => array(
                    $form_type
                )
            );
        break;
    }
    if($params['class']){
        $d_params['class'] = array_merge($params['class'], $d_params['class']);
    }
    $d_params['class'] = implode(' ', $d_params['class']);
    foreach(array_merge($params, $d_params) as $k => $v){
        $params_strings[] = $k . '="' . $v . '"';
    }
    foreach($elements as $name => $element_params){
        $type = reset($element_params);
        $element_params = next($element_params);
        if(isset($values[$name])){
            if($type == 'checkbox'){
                $element_params['checked'] = 'checked';
            }
            $element_params = array_merge($element_params, array('value' => $values[$name]));
        }
        $elements_string .= bootstrapFormCreateInput($type, $name, $element_params, ($errors[$name]) ? $errors[$name] : array(), $form_type);
    }
    if($params['title']){
        $elements_string = '<legend>' . $params['title'] . '</legend>' . $elements_string;
    }
    return sprintf($decorator, implode(' ', $params_strings), $elements_string);
}