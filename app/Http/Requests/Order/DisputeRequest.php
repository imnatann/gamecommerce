<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class DisputeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_item_id' => ['required', 'integer', 'exists:order_items,id'],
            'reason' => ['required', 'string', 'in:wrong_item,not_as_described,damaged,not_delivered,account_issue,other'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'evidence' => ['nullable', 'array', 'max:10'],
            'evidence.*' => ['image', 'mimes:jpeg,png,webp', 'max:5120'],
            'message' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $orderItemId = $this->input('order_item_id');

            if (!$orderItemId) {
                return;
            }

            $orderItem = \App\Models\OrderItem::find($orderItemId);

            if (!$orderItem) {
                return;
            }

            $order = $orderItem->order;

            if ($order && $order->buyer_id !== $this->user()->id) {
                $validator->errors()->add('order_item_id', 'This item does not belong to your order');
            }

            if ($order && !in_array($order->status->value, ['paid', 'processing', 'delivered', 'completed'])) {
                $validator->errors()->add('order_item_id', 'You can only dispute items from orders that have been paid, delivered, or completed');
            }

            $existingDispute = \App\Models\Dispute::where('order_item_id', $orderItemId)
                ->where('buyer_id', $this->user()->id)
                ->whereIn('status', ['open', 'under_review'])
                ->first();

            if ($existingDispute) {
                $validator->errors()->add('order_item_id', 'You already have an active dispute for this item');
            }

            $deliveredAt = $orderItem->delivered_at;
            if ($deliveredAt && $deliveredAt->diffInDays(now()) > config('gamecommerce.dispute.max_days', 7)) {
                $validator->errors()->add('order_item_id', 'Dispute period has expired. You can only dispute within ' . config('gamecommerce.dispute.max_days', 7) . ' days of delivery.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'order_item_id.required' => 'Please select the item to dispute',
            'order_item_id.exists' => 'Selected item does not exist',
            'reason.required' => 'Please select a reason for the dispute',
            'reason.in' => 'Invalid dispute reason',
            'description.required' => 'Please provide a description of the issue',
            'description.min' => 'Description must be at least 20 characters',
            'description.max' => 'Description must not exceed 5000 characters',
            'evidence.max' => 'Maximum 10 evidence files allowed',
            'evidence.*.max' => 'Each evidence file must not exceed 5MB',
            'message.max' => 'Message must not exceed 2000 characters',
        ];
    }
}