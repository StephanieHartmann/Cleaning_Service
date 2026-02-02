"use client";

import React, { useState } from 'react';
import { useRouter } from 'next/navigation';
import { API_BASE } from '@/lib/config';
import Button from '@/components/ui/Button';
import Input from '@/components/ui/Input';
import Card from '@/components/ui/Card';
import Link from 'next/link';
import { ArrowLeft, Save } from 'lucide-react';

export default function CreateOrder() {
    const router = useRouter();

    // Form State
    const [formData, setFormData] = useState({
        first_name: '',
        last_name: '',
        phone: '',
        email: '',
        street: '',
        number: '',
        zip: '',
        city: '',
        cleaning_type: 'Basic',
        size_sqm: ''
    });
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');

    // Handle Input Change
    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    // ----------------------------------------------------------------
    // Submit Handler: Send new order to API
    // ----------------------------------------------------------------
    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        setError('');

        // Basic client-side validation logic
        if (parseFloat(formData.size_sqm) <= 0) {
            setError('Size must be a positive number.');
            setLoading(false);
            return;
        }

        try {
            // POST request to create order
            const res = await fetch(`${API_BASE}/orders.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });

            if (res.ok) {
                // Success: Redirect to dashboard
                router.push('/');
            } else {
                const data = await res.json();
                setError(data.error || 'Failed to create order');
            }
        } catch (err) {
            console.error(err);
            setError('Network error occurred.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="max-w-2xl mx-auto space-y-6">
            <div className="flex items-center gap-4">
                <Link href="/">
                    <Button variant="ghost" size="sm" className="pl-0"> <ArrowLeft size={16} /> Back </Button>
                </Link>
                <h1 className="text-2xl font-bold text-brand-primary">Create new Service Order</h1>
            </div>

            <Card>
                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Name Fields */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Input
                            label="First Name"
                            name="first_name"
                            value={formData.first_name}
                            onChange={handleChange}
                            required
                        />
                        <Input
                            label="Last Name"
                            name="last_name"
                            value={formData.last_name}
                            onChange={handleChange}
                            required
                        />
                    </div>

                    {/* Contact Fields with Validation */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Input
                            label="Phone"
                            name="phone"
                            type="tel"
                            // Pattern allows +, spaces, and numbers. Min length 10 roughly covers valid numbers
                            value={formData.phone}
                            onChange={(e) => {
                                // Allow numbers, spaces, and + at the beginning
                                const val = e.target.value;
                                if (/^[+]?[\d\s]*$/.test(val)) handleChange(e);
                            }}
                            required
                            placeholder="+41 79 000 00 00"
                        />
                        <Input
                            label="Email"
                            name="email"
                            type="email"
                            value={formData.email}
                            onChange={handleChange}
                            required
                            placeholder="example@mail.com"
                        />
                    </div>

                    {/* Address Section */}
                    <div className="space-y-4 pt-4 border-t border-gray-100">
                        <h3 className="text-sm font-medium text-gray-500 uppercase tracking-wider">Address Details</h3>
                        <div className="grid grid-cols-3 gap-4">
                            <div className="col-span-2">
                                <Input
                                    label="Street"
                                    name="street"
                                    value={formData.street}
                                    onChange={handleChange}
                                    required
                                />
                            </div>
                            <Input
                                label="Number"
                                name="number"
                                value={formData.number}
                                onChange={(e) => {
                                    if (/^\d*$/.test(e.target.value)) handleChange(e);
                                }}
                                inputMode="numeric"
                                required
                            />
                        </div>
                        <div className="grid grid-cols-3 gap-4">
                            <Input
                                label="ZIP"
                                name="zip"
                                value={formData.zip}
                                onChange={(e) => {
                                    if (/^\d*$/.test(e.target.value)) handleChange(e);
                                }}
                                inputMode="numeric"
                                required
                            />
                            <div className="col-span-2">
                                <Input
                                    label="City"
                                    name="city"
                                    value={formData.city}
                                    onChange={handleChange}
                                    required
                                />
                            </div>
                        </div>
                    </div>

                    {/* Service Details Section */}
                    <div className="space-y-4 pt-4 border-t border-gray-100">
                        <h3 className="text-sm font-medium text-gray-500 uppercase tracking-wider">Service Details</h3>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Cleaning Type</label>
                                <select
                                    name="cleaning_type"
                                    value={formData.cleaning_type}
                                    onChange={handleChange}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white"
                                >
                                    <option value="Basic">Basic Cleaning</option>
                                    <option value="Standard">Standard Cleaning</option>
                                    <option value="Deep">Deep Cleaning</option>
                                    <option value="Move-out">Move-out Cleaning</option>
                                </select>
                            </div>
                            <Input
                                label="Size (m²)"
                                name="size_sqm"
                                type="number"
                                step="0.01"
                                min="0"
                                value={formData.size_sqm}
                                onChange={handleChange}
                                onKeyDown={(e) => {
                                    // Prevent invalid characters for number input
                                    if (["e", "E", "+", "-"].includes(e.key)) {
                                        e.preventDefault();
                                    }
                                }}
                                required
                            />
                        </div>
                    </div>

                    {/* Error Message Display */}
                    {error && (
                        <div className="bg-red-50 text-red-600 p-3 rounded-md text-sm">
                            {error}
                        </div>
                    )}

                    <div className="flex justify-end pt-4">
                        <Button type="submit" isLoading={loading}>
                            <Save size={18} /> Create Order
                        </Button>
                    </div>
                </form>
            </Card>
        </div>
    );
}
