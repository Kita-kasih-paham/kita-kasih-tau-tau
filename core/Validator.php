<?php

namespace Core;

class Validator
{
    private array $errors = [];
    private array $data   = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /** required|min:3|max:100|numeric|date */
    public function validate(array $rules): self
    {
        foreach ($rules as $field => $ruleStr) {
            $value = trim($this->data[$field] ?? '');
            foreach (explode('|', $ruleStr) as $rule) {
                [$ruleName, $param] = array_pad(explode(':', $rule, 2), 2, null);
                $this->applyRule($field, $value, $ruleName, $param);
            }
        }
        return $this;
    }

    private function applyRule(string $field, string $value, string $rule, ?string $param): void
    {
        $label = ucfirst(str_replace('_', ' ', $field));
        match ($rule) {
            'required' => $value === '' && $this->addError($field, "$label wajib diisi."),
            'min'      => $value !== '' && mb_strlen($value) < (int)$param
                            && $this->addError($field, "$label minimal $param karakter."),
            'max'      => mb_strlen($value) > (int)$param
                            && $this->addError($field, "$label maksimal $param karakter."),
            'numeric'  => $value !== '' && !is_numeric($value)
                            && $this->addError($field, "$label harus berupa angka."),
            'min_val'  => $value !== '' && (float)$value < (float)$param
                            && $this->addError($field, "$label minimal $param."),
            'date'     => $value !== '' && !strtotime($value)
                            && $this->addError($field, "$label bukan format tanggal valid."),
            'confirmed'=> ($this->data[$field . '_confirmation'] ?? '') !== $value
                            && $this->addError($field, "$label konfirmasi tidak cocok."),
            default    => null,
        };
    }

    private function addError(string $field, string $msg): void
    {
        $this->errors[$field][] = $msg;
    }

    public function fails(): bool  { return !empty($this->errors); }
    public function passes(): bool { return empty($this->errors); }

    public function errors(): array { return $this->errors; }

    /** Flat list of all error messages */
    public function messages(): array
    {
        return array_merge(...array_values($this->errors));
    }

    /** First error for a field */
    public function first(string $field): string
    {
        return $this->errors[$field][0] ?? '';
    }
}
