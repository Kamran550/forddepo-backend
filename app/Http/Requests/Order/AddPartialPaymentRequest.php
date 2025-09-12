<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class AddPartialPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Or add your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order_id' => 'required|integer|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'order_id.required' => 'Order ID is required.',
            'order_id.exists' => 'The selected order does not exist.',
            'amount.required' => 'Payment amount is required.',
            'amount.numeric' => 'Payment amount must be a number.',
            'amount.min' => 'Payment amount must be greater than 0.',
            'payment_method.string' => 'Payment method must be a string.',
            'payment_method.max' => 'Payment method cannot exceed 255 characters.',
            'note.string' => 'Note must be a string.',
            'note.max' => 'Note cannot exceed 1000 characters.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $orderId = $this->input('order_id');
            $amount = $this->input('amount');

            if ($orderId && $amount) {
                $order = \App\Models\Order::find($orderId);

                if ($order) {
                    $remainingAmount = $order->total_price - $order->paid_amount;

                    if ($amount > $remainingAmount) {
                        $validator->errors()->add(
                            'amount',
                            'Payment amount cannot exceed remaining balance of ' . number_format($remainingAmount, 2)
                        );
                    }

                    if ($order->is_fully_paid) {
                        $validator->errors()->add(
                            'order_id',
                            'This order is already fully paid.'
                        );
                    }
                }
            }
        });
    }
}
