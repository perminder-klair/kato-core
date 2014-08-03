<?php

namespace kato\gii\generators\crud;

class Generator extends \yii\gii\generators\crud\Generator
{
    /**
     * Generates code for active field
     * @param  string $attribute
     * @return string
     */
    public function generateActiveField($attribute)
    {
        $tableSchema = $this->getTableSchema();
        if ($tableSchema === false || !isset($tableSchema->columns[$attribute])) {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)) {
                return "\$form->field(\$model, '$attribute')->passwordInput()";
            } else {
                return "\$form->field(\$model, '$attribute')";
            }
        }
        $column = $tableSchema->columns[$attribute];

        if ($column->name === 'tags') {
            return "\$form->field(\$model, '$attribute')->widget(Select2::classname(), [
            'language' => 'en',
            'options' => [
                'multiple' => true,
                'placeholder' => 'Select a tag ...'
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'tags' => \$tag->listTags(\$model->className()),
            ],
        ])";
        } elseif ($column->type === 'timestamp') {
            return "\$form->field(\$model, '$attribute')->widget(DatePicker::classname(), [
                'options' => [
                    'placeholder' => 'Select date ...',
                ],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true
                ],
            ])";
        } elseif ($column->phpType === 'boolean' || $column->name === 'active' || $column->name === 'deleted') {
            return "\$form->field(\$model, '$attribute')->widget(SwitchInput::classname(), [
                'pluginOptions' => [
                    'size' => 'small'
                ],
            ])";
        } elseif ($column->type === 'text') {
            return "\$form->field(\$model, '$attribute')->widget(ImperaviWidget::classname())";
        } else {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name)) {
                $input = 'passwordInput';
            } else {
                $input = 'textInput';
            }
            if ($column->phpType !== 'string' || $column->size === null) {
                return "\$form->field(\$model, '$attribute')->$input()";
            } else {
                return "\$form->field(\$model, '$attribute')->$input(['maxlength' => $column->size])";
            }
        }
    }
}
