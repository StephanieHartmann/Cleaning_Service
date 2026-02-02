"use client";

import React, { useState, useEffect } from 'react';
import { useRouter, useParams } from 'next/navigation';
import { API_BASE } from '@/lib/config';
import Button from '@/components/ui/Button';
import Input from '@/components/ui/Input';
import Card from '@/components/ui/Card';
import Link from 'next/link';
import { ArrowLeft, Calendar, User } from 'lucide-react';

export default function ScheduleOrder() {
    const params = useParams();
    const id = params?.id;
    const router = useRouter();

    // Form data state
    const [formData, setFormData] = useState({
        service_date: '',
        service_time: '',
        assigned_to: ''
    });
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');

    // Dynamic Employee List
    const [employees, setEmployees] = useState<string[]>([]);

    // ----------------------------------------------------------------
    // Effect: Load Employees on Mount
    // ----------------------------------------------------------------
    useEffect(() => {
        // Fetch employees from DB API (Dynamic List)
        fetch(`${API_BASE}/employees.php`)
            .then(res => res.json())
            .then(data => setEmployees(data))
            .catch(err => console.error("Failed to load employees", err));
    }, []);

    // ----------------------------------------------------------------
    // Effect: Load Order Details if Editing
    // ----------------------------------------------------------------
    useEffect(() => {
        if (id) fetchOrder();
    }, [id]);

    const fetchOrder = async () => {
        try {
            const res = await fetch(`${API_BASE}/order.php?id=${id}`);
            const data = await res.json();
            if (res.ok) {
                // Populate form with existing data
                setFormData({
                    service_date: data.service_date || '',
                    service_time: data.service_time || '',
                    assigned_to: data.assigned_to || data.employee_name || ''
                });
            }
        } catch (err) {
            console.error(err);
        }
    };

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    // ----------------------------------------------------------------
    // Submit: Schedule the Order
    // ----------------------------------------------------------------
    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        setError('');

        try {
            const res = await fetch(`${API_BASE}/order.php?id=${id}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'schedule', // Specific action for API
                    ...formData
                })
            });

            if (res.ok) {
                // Return to Order Details page
                router.push(`/order/${id}`);
            } else {
                const data = await res.json();
                setError(data.error || 'Failed to schedule');
            }
        } catch (err) {
            console.error(err);
            setError('Network error');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="max-w-xl mx-auto space-y-6">
            <div className="flex items-center gap-4">
                <Link href={`/order/${id}`}>
                    <Button variant="ghost" size="sm" className="pl-0 text-gray-600"> <ArrowLeft size={16} /> Back </Button>
                </Link>
                <h1 className="text-2xl font-bold text-brand-primary">Schedule Service</h1>
            </div>

            <Card>
                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Employee Selection */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Assign Employee</label>
                        <div className="relative">
                            <User className="absolute left-3 top-3 text-gray-400" size={18} />
                            <select
                                name="assigned_to"
                                className="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                                value={formData.assigned_to}
                                onChange={handleChange}
                                required
                            >
                                <option value="">Select Employee...</option>
                                {/* Only show employees from API */}
                                {employees.map(emp => (
                                    <option key={emp} value={emp}>{emp}</option>
                                ))}
                            </select>
                        </div>
                    </div>

                    {/* Date and Time Selection */}
                    <div className="grid grid-cols-2 gap-4">
                        <Input
                            label="Date"
                            type="date"
                            name="service_date"
                            value={formData.service_date}
                            min={new Date().toISOString().split('T')[0]} // Block past dates
                            onChange={handleChange}
                            required
                        />
                        <Input
                            label="Time"
                            type="time"
                            name="service_time"
                            value={formData.service_time}
                            onChange={handleChange}
                            required
                        />
                    </div>

                    {error && (
                        <div className="bg-red-50 text-red-600 p-3 rounded-md text-sm">
                            {error}
                        </div>
                    )}

                    <div className="pt-2">
                        <Button type="submit" className="w-full" isLoading={loading}>
                            <Calendar size={18} /> Confirm Schedule
                        </Button>
                    </div>
                </form>
            </Card>
        </div>
    );
}
