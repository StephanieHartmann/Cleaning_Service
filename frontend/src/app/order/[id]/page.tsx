"use client";

import React, { useEffect, useState } from 'react';
import { useRouter, useParams } from 'next/navigation';
import { API_BASE } from '@/lib/config';
import Button from '@/components/ui/Button';
import Card from '@/components/ui/Card';
import Modal from '@/components/ui/Modal';
import { ArrowLeft, Printer, Trash2, XCircle, CheckCircle, FileText, Calendar } from 'lucide-react';
import Link from 'next/link';

// Define Interface matching DB structure
interface Order {
    order_id: number;
    first_name: string;
    last_name: string;
    address: string;
    phone: string;
    email: string;
    cleaning_type: string;
    size_sqm: number;
    status: string;
    service_date: string;
    service_time: string;
    assigned_to: string;
    employee_name: string;
    hours_worked: string;
    report_text: string;
}

export default function OrderDetails() {
    const params = useParams();
    const id = params?.id;
    const router = useRouter();

    const [order, setOrder] = useState<Order | null>(null);
    const [loading, setLoading] = useState(true);

    // Modal State for Completion Report
    const [showCompleteModal, setShowCompleteModal] = useState(false);
    const [hoursWorked, setHoursWorked] = useState('');
    const [reportText, setReportText] = useState('');

    useEffect(() => {
        if (id) fetchOrder();
    }, [id]);

    // ----------------------------------------------------------------
    // Fetch Order Details
    // ----------------------------------------------------------------
    const fetchOrder = async () => {
        try {
            const res = await fetch(`${API_BASE}/order.php?id=${id}`);
            const data = await res.json();
            if (res.ok) setOrder(data);
            else alert("Order not found");
        } catch (error) {
            console.error(error);
        } finally {
            setLoading(false);
        }
    };

    // ----------------------------------------------------------------
    // Handle Actions (Delete, Cancel, Invoice, Complete)
    // ----------------------------------------------------------------
    const handleAction = async (action: string, extraData = {}) => {
        // Confirmation prompts
        let confirmMsg = '';
        if (action === 'delete') confirmMsg = 'Is this an error? This will PERMANENTLY delete the record.';
        if (action === 'cancel_order') confirmMsg = 'Did the client cancel the job?';
        if (action === 'invoice_order') confirmMsg = 'Mark this order as Billed/Invoiced?';

        // Open Modal logic for completion
        if (action === 'complete_order' && !('confirmed' in extraData)) {
            setShowCompleteModal(true);
            return;
        }

        if (confirmMsg && !window.confirm(confirmMsg)) return;

        try {
            // Send action to API
            const res = await fetch(`${API_BASE}/order.php?id=${id}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action, ...extraData })
            });

            if (res.ok) {
                if (action === 'delete') router.push('/'); // Redirect on delete
                else {
                    fetchOrder(); // Refresh data on status change
                    setShowCompleteModal(false);
                }
            } else {
                alert("Action failed");
            }
        } catch (error) {
            console.error(error);
        }
    };

    // Submits the completion modal form
    const submitCompletion = (e: React.FormEvent) => {
        e.preventDefault();
        handleAction('complete_order', {
            confirmed: true,
            hours_worked: hoursWorked,
            report_text: reportText
        });
    };

    if (loading) return <div className="p-8 text-center text-gray-500">Loading order details...</div>;
    if (!order) return <div className="p-8 text-center text-red-500">Order not found.</div>;

    return (
        <div className="max-w-4xl mx-auto">
            {/* Top Navigation & Print Button */}
            <div className="print:hidden flex justify-between items-center mb-6">
                <Link href="/">
                    <Button variant="ghost" size="sm" className="pl-0 gap-1 text-gray-600">
                        <ArrowLeft size={16} /> Back to List
                    </Button>
                </Link>
                <Button onClick={() => window.print()} variant="secondary" className="gap-2">
                    <Printer size={16} /> Print
                </Button>
            </div>

            {/* Print-Only Header (Visible only on paper) */}
            <div className="hidden print:block mb-8">
                <h2 className="text-xl font-bold">Cleaning Service AG</h2>
                <p className="text-sm">Main Street 10, 8000 Zurich</p>
                <p className="text-sm">Phone: 044 123 45 67 | Email: info@cleaningservice.ch</p>
                <hr className="my-4 border-gray-400" />
            </div>

            <div className="space-y-6 print:space-y-4">
                {/* Main Title */}
                <div className="border-b-2 border-slate-700 pb-2 mb-4 print:border-black">
                    <h1 className="text-3xl font-bold text-brand-primary print:text-2xl">Service Order #{order.order_id}</h1>
                </div>

                {/* Info Card */}
                <Card className="print:shadow-none print:border-none print:p-0">
                    <div className="space-y-6">

                        {/* Section: Customer Info */}
                        <div>
                            <h3 className="text-lg font-semibold text-gray-900 mb-3 print:text-base print:mb-1">Customer Information</h3>
                            <InfoRow label="Name" value={`${order.first_name} ${order.last_name}`} />
                            <InfoRow label="Address" value={order.address} />
                            <InfoRow label="Phone" value={order.phone} />
                            <InfoRow label="Email" value={order.email} />
                        </div>

                        {/* Section: Service Info */}
                        <div>
                            <h3 className="text-lg font-semibold text-gray-900 mt-6 mb-3 print:mt-4 print:mb-1 print:text-base">Service Details</h3>
                            <InfoRow label="Type" value={order.cleaning_type} />
                            <InfoRow label="Size" value={`${order.size_sqm} m²`} />
                            <InfoRow label="Status" value={
                                <span className={`print:border print:border-black print:px-1 print:text-black px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800`}>
                                    {order.status}
                                </span>
                            } />
                        </div>

                        {/* Section: Schedule Info (Conditional) */}
                        {['Scheduled', 'Completed', 'Invoiced'].includes(order.status) && (
                            <div>
                                <h3 className="text-lg font-semibold text-gray-900 mt-6 mb-3 print:mt-4 print:mb-1 print:text-base">Schedule Information</h3>
                                <InfoRow label="Assigned Employee" value={order.assigned_to || order.employee_name} />
                                <InfoRow label="Date" value={order.service_date} />
                                <InfoRow label="Time" value={order.service_time} />
                            </div>
                        )}

                        {/* Section: Work Report (Conditional) */}
                        {(order.status === 'Completed' || order.status === 'Invoiced') && (
                            <div className="print:block">
                                <h3 className="text-lg font-semibold text-gray-900 mt-6 mb-3 print:mt-4 print:mb-1 print:text-base">Work Report</h3>
                                <InfoRow label="Hours Worked" value={`${order.hours_worked || '___'} hours`} />
                                <div className="mt-2">
                                    <strong className="text-sm font-medium text-gray-500 print:text-black">Notes:</strong>
                                    <div className="mt-1 p-3 border border-gray-200 rounded-md min-h-[80px] print:border-black text-sm">
                                        {order.report_text || 'No report text.'}
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Signature Area (Print Only) */}
                        <div className="hidden print:flex justify-between mt-12 pt-8">
                            <div className="w-[45%] border-t border-black pt-2 text-center text-sm">Customer Signature</div>
                            <div className="w-[45%] border-t border-black pt-2 text-center text-sm">Employee Signature</div>
                        </div>

                    </div>
                </Card>

                {/* Workflow Buttons (Hidden on Print) */}
                <div className="print:hidden border-t border-gray-200 pt-6 mt-6 flex flex-col sm:flex-row justify-between gap-4">
                    <Button variant="danger" onClick={() => handleAction('delete')} className="gap-2">
                        <Trash2 size={16} /> Delete Record
                    </Button>

                    <div className="flex flex-wrap gap-2">
                        {order.status !== 'Cancelled' && order.status !== 'Invoiced' && (
                            <Button variant="secondary" onClick={() => handleAction('cancel_order')} className="gap-2">
                                <XCircle size={16} /> Cancel
                            </Button>
                        )}

                        {order.status === 'New' && (
                            <Link href={`/schedule/${order.order_id}`}>
                                <Button variant="warning" className="gap-2 bg-orange-500 hover:bg-orange-600 text-white">
                                    <Calendar size={16} /> Schedule Now
                                </Button>
                            </Link>
                        )}

                        {order.status === 'Scheduled' && (
                            <>
                                <Link href={`/schedule/${order.order_id}`}>
                                    <Button variant="warning" className="gap-2">
                                        <Calendar size={16} /> Change Date
                                    </Button>
                                </Link>
                                <Button variant="primary" onClick={() => handleAction('complete_order')} className="gap-2 bg-green-600 hover:bg-green-700">
                                    <CheckCircle size={16} /> Mark as Done
                                </Button>
                            </>
                        )}

                        {order.status === 'Completed' && (
                            <Button variant="primary" onClick={() => handleAction('invoice_order')} className="gap-2 bg-purple-600 hover:bg-purple-700">
                                <FileText size={16} /> Mark as Invoiced
                            </Button>
                        )}
                    </div>
                </div>
            </div>

            {/* Completion Modal Definition */}
            <Modal isOpen={showCompleteModal} onClose={() => setShowCompleteModal(false)} title="Complete Order & Report">
                <form onSubmit={submitCompletion} className="space-y-4">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Hours Worked</label>
                        <input
                            type="number"
                            step="any"
                            required
                            autoFocus
                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none"
                            value={hoursWorked}
                            onChange={(e) => setHoursWorked(e.target.value)}
                        />
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Work Report / Notes</label>
                        <textarea
                            required
                            rows={4}
                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none"
                            value={reportText}
                            onChange={(e) => setReportText(e.target.value)}
                        ></textarea>
                    </div>
                    <div className="flex gap-3 pt-2">
                        <Button type="submit" className="flex-1 bg-green-600 hover:bg-green-700">Confirm</Button>
                        <Button type="button" variant="secondary" className="flex-1" onClick={() => setShowCompleteModal(false)}>Cancel</Button>
                    </div>
                </form>
            </Modal>

            {/* Custom Print Styles */}
            <style jsx global>{`
        @media print {
          @page { size: A4; margin: 20mm; }
          body { font-size: 12px; background: white; }
          .print\\:hidden { display: none !important; }
        }
      `}</style>
        </div>
    );
}

// Helper Component for Info Rows
const InfoRow = ({ label, value }: { label: string, value: React.ReactNode }) => (
    <div className="flex justify-between py-2 border-b border-gray-100 print:border-gray-300 print:py-1">
        <span className="font-medium text-gray-600 print:text-black">{label}:</span>
        <span className="text-gray-900 print:text-black text-right">{value}</span>
    </div>
);
