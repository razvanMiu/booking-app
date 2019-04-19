<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBooking extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'event.title' => 'required|min:6|max:15',
            'event.description' =>'required|min:6|max:255',
        ];
    }

    public function messages()
    {
        return [
            'event.title.required' => 'The title may not be lesser than 6 characters and greater than 15 characters.',
            'event.title.min' => 'The title must be at least 6 characters.',
            'event.title.max' => 'The title must be lesser than 15 characters.',
            'event.description.required' => 'The event description may not be lesser than 6 characters and greater than 255 characters.',
            'event.description.min' => 'The event description must be at least 6 characters.',
            'event.description.max' => 'The event description must be lesser than 255 characters.',
        ];
    }
}
